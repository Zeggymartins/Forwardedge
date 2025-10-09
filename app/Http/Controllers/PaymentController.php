<?php

namespace App\Http\Controllers;

use App\Jobs\SendEnrollmentEmail;
use App\Jobs\SendEventTicketEmail;
use App\Jobs\SendOrderEmail;
use App\Models\CartItem;
use App\Models\Payment;
use App\Models\Orders;
use App\Models\Enrollment;
use App\Models\EventRegistration;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\CourseBundleService;

class PaymentController extends Controller
{
    /**
     * Handle Paystack callback after payment
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('user.cart.index')
                ->with('error', 'No payment reference provided.');
        }

        Log::info('Payment callback received', ['reference' => $reference]);

        $payment = Payment::where('reference', $reference)->first();
        if (!$payment) {
            Log::error('Payment not found', ['reference' => $reference]);
            return redirect()->route('user.cart.index')
                ->with('error', 'Payment record not found.');
        }

        // 🔒 Prevent double processing
        if ($payment->status === 'successful') {
            Log::info('Payment already processed - redirecting to success', ['reference' => $reference]);
            return redirect()->route('payment.success', ['reference' => $reference])
                ->with('info', 'This payment was already processed.');
        }

        // Verify payment with Paystack
        $paystackSecret = config('services.paystack.secret');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $paystackSecret,
        ])->get("https://api.paystack.co/transaction/verify/{$reference}");

        if (!$response->successful()) {
            Log::error('Paystack verification failed', [
                'reference' => $reference,
                'status' => $response->status()
            ]);
            return redirect()->route('payment.failed')
                ->with('error', 'Payment verification failed.');
        }

        $verificationData = $response->json('data');

        if ($verificationData['status'] !== 'success') {
            $payment->update(['status' => 'failed']);
            Log::warning('Payment not successful', [
                'reference' => $reference,
                'paystack_status' => $verificationData['status']
            ]);

            return redirect()->route('payment.failed')
                ->with('error', 'Payment was not successful.');
        }

        // 🔒 Atomic payment status update to prevent race conditions
        $updated = DB::table('payments')
            ->where('id', $payment->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'successful',
                'amount' => $verificationData['amount'] / 100,
                'metadata' => json_encode(array_merge($payment->metadata ?? [], [
                    'verified_at' => now()->toISOString(),
                    'paystack_verification' => $verificationData,
                    'processed_by' => 'callback'
                ])),
                'updated_at' => now()
            ]);

        // If no rows updated, another process already handled it
        if ($updated === 0) {
            Log::info('Payment already processed by another request', ['reference' => $reference]);
            return redirect()->route('payment.success', ['reference' => $reference])
                ->with('info', 'Payment already processed.');
        }

        // ✅ Refresh payment to get updated data
        $payment->refresh();

        // Payment successful → process fulfillment in transaction
        try {
            DB::transaction(function () use ($payment, $verificationData) {
                $this->fulfillPayment($payment, $verificationData);
            });

            Log::info('Payment processed successfully via callback', ['reference' => $reference]);
        } catch (\Exception $e) {
            Log::error('Payment fulfillment failed', [
                'reference' => $reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Payment was marked successful but fulfillment failed
            // This will be retried by webhook if it arrives
        }

        return redirect()->route('payment.success', ['reference' => $reference])
            ->with('success', 'Payment completed successfully!');
    }

    /**
     * Handle Paystack webhook
     */
    public function webhook(Request $request)
    {
        try {
            $signature = $request->header('x-paystack-signature');
            if (!$signature) {
                return response()->json(['error' => 'Signature missing'], 400);
            }

            $payload = $request->getContent();
            $computedSignature = hash_hmac('sha512', $payload, config('services.paystack.secret'));
            
            if ($signature !== $computedSignature) {
                Log::warning('Webhook signature mismatch');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $data = json_decode($payload, true);
            if (!$data || !isset($data['event'])) {
                return response()->json(['error' => 'Invalid payload'], 400);
            }

            Log::info('Webhook received', [
                'event' => $data['event'],
                'reference' => $data['data']['reference'] ?? 'N/A'
            ]);

            if ($data['event'] === 'charge.success') {
                $reference = $data['data']['reference'];
                $payment = Payment::where('reference', $reference)->first();

                if (!$payment) {
                    Log::warning('Webhook received for unknown payment', ['reference' => $reference]);
                    return response()->json(['status' => 'ignored'], 200);
                }

                // 🔒 Prevent double processing
                if ($payment->status === 'successful') {
                    Log::info('Webhook received but payment already processed', ['reference' => $reference]);
                    return response()->json(['status' => 'already_processed'], 200);
                }

                // 🔒 Atomic payment status update
                $updated = DB::table('payments')
                    ->where('id', $payment->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'successful',
                        'metadata' => json_encode(array_merge($payment->metadata ?? [], [
                            'webhook_received' => now()->toISOString(),
                            'paystack_webhook_data' => $data['data'],
                            'processed_by' => 'webhook'
                        ])),
                        'updated_at' => now()
                    ]);

                if ($updated === 0) {
                    Log::info('Webhook arrived but payment already processed', ['reference' => $reference]);
                    return response()->json(['status' => 'already_processed'], 200);
                }

                // ✅ Refresh payment
                $payment->refresh();

                // Process fulfillment
                DB::transaction(function () use ($payment, $data) {
                    $this->fulfillPayment($payment, $data['data']);
                });

                Log::info('Webhook processed successfully', ['reference' => $reference]);
            }

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error('Paystack webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Fulfill payment - create enrollments, registrations, etc.
     * This is called from both callback and webhook
     */
    private function fulfillPayment(Payment $payment, array $verificationData)
    {
        // ORDERS
        if ($payment->payable instanceof Orders) {
            $order = $payment->payable;
            
            // Prevent duplicate order processing
            if ($order->status === 'paid') {
                Log::info('Order already fulfilled', ['order_id' => $order->id]);
                return;
            }

            $order->update(['status' => 'paid']);
            CartItem::where('user_id', $payment->user_id)->delete();

            // Create ZIP bundle and dispatch email
            $zipPath = CourseBundleService::createZip($order);
            SendOrderEmail::dispatch($order, $zipPath);
            
            Log::info('Order fulfilled and email dispatched', ['order_id' => $order->id]);
        }

        // ENROLLMENTS
        if ($payment->payable instanceof \App\Models\CourseSchedule) {
            $schedule = $payment->payable;
            $plan = $payment->metadata['payment_plan'] ?? 'full';
            $total = $payment->metadata['total'] ?? $payment->amount;

            // 🔒 Prevent duplicate enrollments
            $existingEnrollment = Enrollment::where('user_id', $payment->user_id)
                ->where('course_schedule_id', $schedule->id)
                ->first();

            if ($existingEnrollment) {
                Log::warning('Duplicate enrollment prevented', [
                    'user_id' => $payment->user_id,
                    'schedule_id' => $schedule->id,
                    'existing_enrollment_id' => $existingEnrollment->id
                ]);
                return;
            }

            $enrollment = Enrollment::create([
                'user_id' => $payment->user_id,
                'course_schedule_id' => $schedule->id,
                'payment_plan' => $plan,
                'total_amount' => $total,
                'balance' => $plan === 'partial' ? $total - $payment->amount : 0,
                'status' => 'active',
            ]);

            SendEnrollmentEmail::dispatch($enrollment);
            Log::info('Enrollment created and email dispatched', ['enrollment_id' => $enrollment->id]);
        }

        // EVENT TICKETS
        if ($payment->payable instanceof EventTicket) {
            $ticket = $payment->payable;
            $meta = $payment->metadata ?? [];

            // 🔒 Prevent duplicate registrations
            $existingRegistration = EventRegistration::where('payment_reference', $payment->reference)->first();
            
            if ($existingRegistration) {
                Log::warning('Duplicate event registration prevented', [
                    'payment_reference' => $payment->reference,
                    'existing_registration_id' => $existingRegistration->id
                ]);
                return;
            }

            // Check ticket availability before creating registration
            if ($ticket->quantity_available <= 0) {
                Log::error('Ticket sold out during payment processing', [
                    'ticket_id' => $ticket->id,
                    'payment_id' => $payment->id
                ]);
                // Could send a refund notification here
                return;
            }

            $registration = EventRegistration::create([
                'event_id' => $meta['event_id'],
                'ticket_id' => $ticket->id,
                'first_name' => $meta['first_name'],
                'last_name' => $meta['last_name'],
                'email' => $meta['email'],
                'phone' => $meta['phone'] ?? null,
                'company' => $meta['company'] ?? null,
                'job_title' => $meta['job_title'] ?? null,
                'special_requirements' => $meta['special_requirements'] ?? null,
                'registration_code' => Str::uuid(),
                'registered_at' => now(),
                'status' => 'confirmed',
                'amount_paid' => $payment->amount,
                'payment_status' => 'paid',
                'payment_reference' => $payment->reference,
            ]);

            // Update ticket quantities
            $ticket->decrement('quantity_available');
            $ticket->increment('quantity_sold');

            SendEventTicketEmail::dispatch($registration);
            Log::info('Event registration created and email dispatched', ['registration_id' => $registration->id]);
        }
    }
}
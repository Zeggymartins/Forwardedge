<?php

namespace App\Http\Controllers;

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

class PaymentController extends Controller
{
 

    public function callback(Request $request)
    {
        try {
            $reference = $request->query('reference');

            if (!$reference) {
                return redirect()->route('user.cart.index')
                    ->with('error', 'No payment reference provided.');
            }

            Log::info('Payment callback received', ['reference' => $reference]);

            // Find the payment record
            $payment = Payment::where('reference', $reference)->first();

            if (!$payment) {
                Log::error('Payment not found for callback', ['reference' => $reference]);
                return redirect()->route('user.cart.index')
                    ->with('error', 'Payment record not found.');
            }

            // Verify with Paystack
            $paystackSecret = config('services.paystack.secret');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecret,
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if (!$response->successful()) {
                Log::error('Paystack verification failed', [
                    'reference' => $reference,
                    'status'    => $response->status()
                ]);
                return redirect()->route('payment.failed')
                    ->with('error', 'Payment verification failed.');
            }

            $verificationData = $response->json('data');

            if ($verificationData['status'] === 'success') {
                Log::info('Payment verified successfully', ['reference' => $reference]);

                DB::transaction(function () use ($payment, $verificationData, $reference) {
                    // Verified amount (kobo → naira)
                    $verifiedAmount = $verificationData['amount'] / 100;

                    // Update payment status
                    $payment->update([
                        'status'  => 'successful',
                        'amount'  => $verifiedAmount,
                        'metadata'=> array_merge($payment->metadata ?? [], [
                            'verified_at' => now()->toISOString(),
                            'paystack_verification' => $verificationData
                        ])
                    ]);

                    /**
                     * 🛒 Case 1: Order payment
                     */
                    if ($payment->payable instanceof Orders) {
                        $payment->payable->update(['status' => 'paid']);

                        // Clear user cart
                        $cartItemsDeleted = CartItem::where('user_id', $payment->user_id)->delete();

                        Log::info('Cart cleared after successful order payment', [
                            'user_id' => $payment->user_id,
                            'deleted' => $cartItemsDeleted,
                            'reference' => $reference
                        ]);
                    }

                    /**
                     * 🎓 Case 2: Course Schedule payment (Enrollment)
                     */
                    if ($payment->payable instanceof \App\Models\CourseSchedule) {
                        $schedule = $payment->payable;
                        $plan     = $payment->metadata['payment_plan'] ?? 'full';
                        $total    = $payment->metadata['total'] ?? $verifiedAmount;

                        // Calculate balance (for partial plan)
                        $balance = $plan === 'partial'
                            ? $total - $verifiedAmount
                            : 0;

                        Enrollment::create([
                            'user_id'            => $payment->user_id,
                            'course_schedule_id' => $schedule->id,
                            'payment_plan'       => $plan,
                            'total_amount'       => $total,
                            'balance'            => $balance,
                            'status'             => 'active',
                        ]);

                        Log::info('Enrollment created after schedule payment', [
                            'user_id'     => $payment->user_id,
                            'schedule_id' => $schedule->id,
                            'reference'   => $reference
                        ]);
                    }

                    /**
                     * 🎟️ Case 3: Event Ticket payment (Registration)
                     */
                    if ($payment->payable instanceof EventTicket) {
                        $ticket = $payment->payable;
                        $meta   = $payment->metadata ?? [];

                        // Ensure tickets are still available
                        if ($ticket->quantity_available <= 0) {
                            throw new \Exception('Ticket is sold out.');
                        }

                        // Create event registration
                        $registration = EventRegistration::create([
                            'event_id'            => $meta['event_id'],
                            'ticket_id'           => $ticket->id,
                            'first_name'          => $meta['first_name'],
                            'last_name'           => $meta['last_name'],
                            'email'               => $meta['email'],
                            'phone'               => $meta['phone'],
                            'company'             => $meta['company'],
                            'job_title'           => $meta['job_title'],
                            'special_requirements'=> $meta['special_requirements'],
                            'registration_code'   => Str::uuid(),
                            'registered_at'       => now(),
                            'status'              => 'confirmed',
                            'amount_paid'         => $verifiedAmount,
                            'payment_status'      => 'paid',
                            'payment_reference'   => $payment->reference,
                        ]);

                        // Update ticket counts
                        $ticket->decrement('quantity_available');
                        $ticket->increment('quantity_sold');

                        Log::info('Event registration created after ticket payment', [
                            'registration_id' => $registration->id,
                            'ticket_id'       => $ticket->id,
                            'reference'       => $reference
                        ]);
                    }
                });

                return redirect()->route('payment.success', ['reference' => $reference])
                    ->with('success', 'Payment completed successfully!');
            } else {
                Log::warning('Payment not successful', [
                    'reference' => $reference,
                    'paystack_status' => $verificationData['status']
                ]);

                $payment->update(['status' => 'failed']);

                return redirect()->route('payment.failed')
                    ->with('error', 'Payment was not successful.');
            }
        } catch (\Exception $e) {
            Log::error('Payment callback error', [
                'error' => $e->getMessage(),
                'reference' => $request->query('reference')
            ]);

            return redirect()->route('payment.failed')
                ->with('error', 'An error occurred while processing your payment.');
        }
    }





    public function webhook(Request $request)
    {
        try {
            // Verify Paystack signature
            $signature = $request->header('x-paystack-signature');
            if (!$signature) {
                Log::warning('Webhook signature missing');
                return response()->json(['error' => 'Signature missing'], 400);
            }

            $payload = $request->getContent();
            $computedSignature = hash_hmac('sha512', $payload, config('services.paystack.secret'));

            if ($signature !== $computedSignature) {
                Log::warning('Invalid webhook signature');
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

            // Handle successful charge
            if ($data['event'] === 'charge.success') {
                $reference = $data['data']['reference'];

                $payment = Payment::where('reference', $reference)->first();
                if ($payment && $payment->status !== 'successful') {

                    DB::transaction(function () use ($payment, $data) {
                        // Update payment
                        $payment->update([
                            'status' => 'successful',
                            'metadata' => array_merge($payment->metadata ?? [], [
                                'webhook_received' => now()->toISOString(),
                                'paystack_webhook_data' => $data['data'],
                            ])
                        ]);

                        // Update related order
                        if ($payment->payable instanceof Orders) {
                            $payment->payable->update(['status' => 'paid']);
                        }

                        // Clear cart items if not already done
                        CartItem::where('user_id', $payment->user_id)->delete();
                    });

                    Log::info('Webhook processed successfully', ['reference' => $reference]);
                }
            }

            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            Log::error("Paystack webhook error: " . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}
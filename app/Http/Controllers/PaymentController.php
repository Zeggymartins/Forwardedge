<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Payment;
use App\Models\Orders;
use App\Models\Enrollment;
use App\Models\EventRegistration;
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
                return redirect()->route('user.cart.index')->with('error', 'No payment reference provided.');
            }

            Log::info('Payment callback received', ['reference' => $reference]);

            // Find the payment record
            $payment = Payment::where('reference', $reference)->first();

            if (!$payment) {
                Log::error('Payment not found for callback', ['reference' => $reference]);
                return redirect()->route('user.cart.index')->with('error', 'Payment record not found.');
            }

            // Verify with Paystack
            $paystackSecret = config('services.paystack.secret');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecret,
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if (!$response->successful()) {
                Log::error('Paystack verification failed', [
                    'reference' => $reference,
                    'status' => $response->status()
                ]);
                return redirect()->route('payment.failed')->with('error', 'Payment verification failed.');
            }

            $verificationData = $response->json('data');

            if ($verificationData['status'] === 'success') {

                Log::info('Payment verified successfully', ['reference' => $reference]);

                // 🎉 PAYMENT SUCCESS - Update all records
                DB::transaction(function () use ($payment, $verificationData, $reference) {

                    // Update payment status
                    $payment->update([
                        'status' => 'successful',
                        'metadata' => array_merge($payment->metadata ?? [], [
                            'verified_at' => now()->toISOString(),
                            'paystack_verification' => $verificationData
                        ])
                    ]);

                    // Update order status
                    if ($payment->payable instanceof Orders) {
                        $payment->payable->update(['status' => 'paid']);

                        Log::info('Order marked as paid', ['order_id' => $payment->payable->id]);
                    }

                    // 🧹 CLEAR CART ITEMS (this was missing!)
                    $cartItemsDeleted = CartItem::where('user_id', $payment->user_id)->delete();

                    Log::info('Cart cleared after successful payment', [
                        'user_id' => $payment->user_id,
                        'items_deleted' => $cartItemsDeleted,
                        'reference' => $reference
                    ]);
                });

                return redirect()->route('payment.success', ['reference' => $reference])
                    ->with('success', 'Payment completed successfully! Your cart has been cleared.');
            } else {
                Log::warning('Payment not successful', [
                    'reference' => $reference,
                    'paystack_status' => $verificationData['status']
                ]);

                // Mark payment as failed
                $payment->update(['status' => 'failed']);

                return redirect()->route('payment.failed')
                    ->with('error', 'Payment was not successful. Your cart items are still available.');
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
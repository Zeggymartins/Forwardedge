<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Orders;
use App\Models\Enrollment;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function initialize(Request $request)
    {
        try {
            $user = Auth::user();

            $data = $request->validate([
                'order_id' => 'required|exists:orders,id',
            ]);

            $order = Orders::findOrFail($data['order_id']);

            if ($order->status !== 'unpaid') {
                return response()->json(['error' => 'Order already processed'], 400);
            }

            $reference = 'FEC_' . strtoupper(Str::random(12));

            $paystackSecret = config('services.paystack.secret');
            if (!$paystackSecret) {
                return response()->json(['error' => 'Payment service not configured'], 500);
            }

            $paystackPayload = [
                'email'        => $user->email,
                'amount'       => intval($order->total * 100), // kobo
                'reference'    => $reference,
                'currency'     => 'NGN',
                'callback_url' => route('payment.callback'),
                'metadata'     => [
                    'user_id'  => $user->id,
                    'order_id' => $order->id,
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecret,
                'Content-Type'  => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', $paystackPayload);

            if (!$response->successful()) {
                return response()->json(['error' => 'Could not initialize payment'], 500);
            }

            $responseData = $response->json();

            if (!isset($responseData['data']['authorization_url'])) {
                return response()->json(['error' => 'Invalid response from payment provider'], 500);
            }

            // Create payment record (pending)
            Payment::create([
                'user_id'      => $user->id,
                'payable_type' => Orders::class,
                'payable_id'   => $order->id,
                'amount'       => $order->total,
                'currency'     => 'NGN',
                'status'       => 'pending',
                'reference'    => $reference,
                'method'       => 'paystack',
                'metadata'     => [
                    'order_id' => $order->id,
                    'user_ip'  => $request->ip(),
                ],
            ]);

            return response()->json([
                'success'           => true,
                'authorization_url' => $responseData['data']['authorization_url'],
                'reference'         => $reference,
            ]);
        } catch (\Exception $e) {
            Log::error('Payment initialization failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Payment initialization failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function callback(Request $request)
    {
        try {
            $reference = $request->query('reference');
            if (!$reference) {
                return response()->json(['error' => 'No reference provided'], 400);
            }

            $paystackSecret = config('services.paystack.secret');
            $verifyResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $paystackSecret,
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if (!$verifyResponse->successful()) {
                return response()->json(['error' => 'Verification failed'], 500);
            }

            $verification = $verifyResponse->json();
            $payment = Payment::where('reference', $reference)->firstOrFail();
            $order = Orders::findOrFail($payment->payable_id);

            if ($verification['data']['status'] === 'success') {
                $payment->update(['status' => 'success']);
                $order->update(['status' => 'paid']);
            } else {
                $payment->update(['status' => 'failed']);
                $order->update(['status' => 'failed']);
            }

            return response()->json(['success' => true, 'status' => $payment->status]);
        } catch (\Exception $e) {
            Log::error('Payment callback failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Payment callback failed', 'message' => $e->getMessage()], 500);
        }
    }

  public function webhook(Request $request)
    {
        try {
            // ✅ Verify Paystack signature
            $signature = $request->header('x-paystack-signature');
            if (!$signature) {
                return response()->json(['error' => 'Signature missing'], 400);
            }

            $payload = $request->getContent();
            $computedSignature = hash_hmac('sha512', $payload, env('PAYSTACK_SECRET_KEY'));
            if ($signature !== $computedSignature) {
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $data = json_decode($payload, true);
            if (!$data || !isset($data['event'])) {
                return response()->json(['error' => 'Invalid payload'], 400);
            }

            // ✅ Handle successful charge
            if ($data['event'] === 'charge.success') {
                $reference = $data['data']['reference'];

                $payment = Payment::where('reference', $reference)->first();
                if ($payment) {
                    $payment->update([
                        'status' => 'paid',
                        'metadata' => array_merge($payment->metadata ?? [], [
                            'paystack_event' => $data['event'],
                            'paystack_id'    => $data['data']['id'],
                        ]),
                    ]);

                    // ✅ Update related order
                    if ($payment->payable_type === Orders::class) {
                        $order = $payment->payable;
                        if ($order) {
                            $order->update(['status' => 'paid']);
                        }
                    }
                }
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error("Paystack Webhook Error: " . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }



    private function updatePayableStatus(Payment $payment)
    {
        try {
            $payable = $payment->payable;

            if (!$payable) {
                Log::warning('Payable not found', ['payment_id' => $payment->id]);
                return;
            }

            if ($payable instanceof Orders) {
                $payable->update(['status' => 'paid']);
            } elseif ($payable instanceof Enrollment) {
                $payable->update(['status' => 'active']);
            } elseif ($payable instanceof EventRegistration) {
                $payable->update(['payment_status' => 'paid', 'status' => 'confirmed']);
            }

            Log::info('Payable status updated', [
                'payable_type' => get_class($payable),
                'payable_id' => $payable->id
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update payable status', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Debug method - remove in production
    public function debug(Request $request)
    {
        if (!config('app.debug')) {
            abort(404);
        }

        return response()->json([
            'auth_check' => Auth::check(),
            'user_id' => Auth::id(),
            'request_method' => $request->method(),
            'request_path' => $request->path(),
            'paystack_configured' => !empty(config('services.paystack.secret')),
            'csrf_token' => csrf_token(),
        ]);
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('x-paystack-signature');

        // Verify webhook signature
        if ($signature !== hash_hmac('sha512', $payload, config('services.paystack.secret'))) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);

        if ($data['event'] === 'charge.success') {
            $reference = $data['data']['reference'];

            $payment = Payment::where('reference', $reference)->first();
            if ($payment) {
                $payment->update(['status' => 'success']);
                $payment->order->update(['status' => 'paid']);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Course;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Check authentication
            if (!Auth::check()) {
                return response()->json(['error' => 'You must be logged in to proceed.'], 401);
            }

            $user = Auth::user();

            // Get cart items and calculate total
            $cartItems = CartItem::where('user_id', $user->id)->with(['course', 'courseContent'])->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Your cart is empty.'], 400);
            }

            $amount = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            // Generate unique reference BEFORE any DB operations
            $reference = 'FEC_' . strtoupper(Str::random(12));

            Log::info('Starting payment initialization', [
                'user_id' => $user->id,
                'amount' => $amount,
                'reference' => $reference
            ]);

            // 🔥 FIRST - Try Paystack initialization (NO DB writes yet)
            $paystackResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.paystack.secret'),
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $user->email,
                'amount' => intval($amount * 100), // Convert to kobo
                'reference' => $reference,
                'currency' => 'NGN',
                'callback_url' => route('payment.callback'),
                'metadata' => [
                    'user_id' => $user->id,
                    'cart_items_count' => $cartItems->count(),
                    'items' => $cartItems->map(function ($item) {
                        return [
                            'course_id' => $item->course_id,
                            'course_name' => $item->course->name,
                            'price' => $item->price,
                            'quantity' => $item->quantity
                        ];
                    })->toArray()
                ],
            ]);

            // Check if Paystack initialization failed
            if (!$paystackResponse->successful()) {
                Log::error('Paystack initialization failed', [
                    'status' => $paystackResponse->status(),
                    'response' => $paystackResponse->body(),
                    'reference' => $reference
                ]);
                return response()->json(['error' => 'Payment service unavailable. Please try again.'], 500);
            }

            $paystackData = $paystackResponse->json();

            if (!isset($paystackData['data']['authorization_url'])) {
                Log::error('Invalid Paystack response - no authorization URL', ['data' => $paystackData]);
                return response()->json(['error' => 'Payment initialization failed.'], 500);
            }

            // 🎉 SUCCESS! Now create DB records since Paystack accepted the transaction

            // Create Order
            $order = Orders::create([
                'user_id' => $user->id,
                'status' => 'pending', // Will change to 'paid' after verification
                'total_price' => $amount,
            ]);

            // Create order items (if you have OrderItem model)
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $item->course_id,
                    'course_content_id' => $item->course_content_id,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                ]);
            }

            // Create Payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'payable_id' => $order->id,
                'payable_type' => Orders::class,
                'amount' => $amount,
                'currency' => 'NGN',
                'status' => 'pending', // Will change to 'successful' after verification
                'reference' => $reference,
                'method' => 'paystack',
                'metadata' => [
                    'cart_items_snapshot' => $cartItems->toArray(),
                    'paystack_data' => $paystackData['data'],
                    'initialized_at' => now()->toISOString(),
                ],
            ]);

            Log::info('Order and payment created successfully', [
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'reference' => $reference
            ]);

            return response()->json([
                'success' => true,
                'authorization_url' => $paystackData['data']['authorization_url'],
                'reference' => $reference,
                'order_id' => $order->id
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'error' => 'An unexpected error occurred. Please try again.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function success(Request $request)
    {
        $reference = $request->query('reference');
        return view('user.pages.payment_success', compact('reference'));
    }

    public function cancel()
    {
        return view('user.pages.payment_failed');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Course;
use App\Models\OrderItem;
use App\Models\Orders;
use App\Models\Payment;
use App\Services\CurrencyHelper;
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

            // Detect currency based on user location
            $currency = CurrencyHelper::current();

            // Get cart items and calculate total in user's currency
            $cartItems = CartItem::where('user_id', $user->id)->with(['course', 'courseContent'])->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Your cart is empty.'], 400);
            }

            // Calculate total using the appropriate price field for the currency
            $amount = $cartItems->sum(function ($item) use ($currency) {
                // Use USD price if available and user is international
                if ($currency === 'USD' && $item->courseContent && $item->courseContent->price_usd) {
                    return $item->courseContent->price_usd * $item->quantity;
                }
                return $item->price * $item->quantity;
            });

            // Generate unique reference BEFORE any DB operations
            $reference = 'FEC_' . strtoupper(Str::random(12));

            Log::info('Starting payment initialization', [
                'user_id' => $user->id,
                'amount' => $amount,
                'currency' => $currency,
                'reference' => $reference
            ]);

            // 🔥 FIRST - Try Paystack initialization (NO DB writes yet)
            $paystackResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.paystack.secret'),
                'Content-Type' => 'application/json',
            ])->post('https://api.paystack.co/transaction/initialize', [
                'email' => $user->email,
                'amount' => CurrencyHelper::toSmallestUnit($amount), // Convert to kobo/cents
                'reference' => $reference,
                'currency' => $currency, // NGN or USD based on detection
                'callback_url' => route('payment.callback'),
                'metadata' => [
                    'user_id' => $user->id,
                    'currency' => $currency,
                    'cart_items_count' => $cartItems->count(),
                    'items' => $cartItems->map(function ($item) {
                        return [
                            'course_id' => $item->course_id,
                            'course_name' => $item->course->name ?? $item->course->title ?? 'Course',
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

            // Create Order with currency
            $order = Orders::create([
                'user_id' => $user->id,
                'status' => 'pending', // Will change to 'paid' after verification
                'total_price' => $amount,
                'currency' => $currency,
            ]);

            // Create order items with currency
            foreach ($cartItems as $item) {
                // Get the correct price for the currency
                $itemPrice = $item->price;
                if ($currency === 'USD' && $item->courseContent && $item->courseContent->price_usd) {
                    $itemPrice = $item->courseContent->price_usd;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $item->course_id,
                    'course_content_id' => $item->course_content_id,
                    'price' => $itemPrice,
                    'currency' => $currency,
                    'quantity' => $item->quantity,
                ]);
            }

            // Create Payment record with currency
            $payment = Payment::create([
                'user_id' => $user->id,
                'payable_id' => $order->id,
                'payable_type' => Orders::class,
                'amount' => $amount,
                'currency' => $currency,
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

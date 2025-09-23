<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Course;
use App\Models\Orders;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            // ✅ Ensure user is logged in
            if (!Auth::check()) {
                return response()->json(['error' => 'You must be logged in to proceed.'], 401);
            }

            $user = Auth::user();

            // ✅ Calculate amount from Cart Items instead of trusting request
            $cartItems = CartItem::where('user_id', $user->id)->with('course')->get();

            if ($cartItems->isEmpty()) {
                return response()->json(['error' => 'Your cart is empty.'], 400);
            }

            $amount = $cartItems->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            // ✅ Create Order
            $order = Orders::create([
                'user_id'     => $user->id,
                'status'      => 'pending',
                'total_price' => $amount,
            ]);

            // (optional) Save items into order_items table if you have one
            // foreach ($cartItems as $item) {
            //     $order->items()->create([
            //         'course_id' => $item->course_id,
            //         'price'     => $item->price,
            //         'quantity'  => $item->quantity,
            //     ]);
            // }

            // ✅ Generate a unique reference before saving
            $reference = uniqid('ref_'); // you control reference, safer for matching

            // ✅ Create Payment record (polymorphic)
            $payment = Payment::create([
                'user_id'      => $user->id,
                'payable_id'   => $order->id,
                'payable_type' => Orders::class,
                'amount'       => $amount,
                'status'       => 'pending',
                'method'       => 'paystack',
                'currency'     => 'NGN',
                'reference'    => $reference, // ✅ required upfront
                'metadata'     => [
                    'cart_items' => $cartItems->toArray(),
                ],
            ]);

            // ✅ Initialize Paystack payment
            $response = Http::withToken(config('services.paystack.secret'))
                ->post('https://api.paystack.co/transaction/initialize', [
                    'email'        => $user->email,
                    'amount'       => $amount * 100, // convert to kobo
                    'reference'    => $reference,    // ✅ send same reference to Paystack
                    'callback_url' => route('payment.callback'),
                ]);

            if (!$response->successful()) {
                Log::error('Paystack initialization failed', ['response' => $response->body()]);
                return response()->json(['error' => 'Paystack initialization failed.'], 500);
            }

            $data = $response->json();

            if (!isset($data['data']['authorization_url'])) {
                Log::error('Invalid Paystack response', ['data' => $data]);
                return response()->json(['error' => 'Invalid Paystack response'], 500);
            }

            // (optional) update metadata with Paystack response
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'paystack_response' => $data['data'],
                ]),
            ]);

            return response()->json([
                'authorization_url' => $data['data']['authorization_url'],
            ]);
        } catch (\Exception $e) {
            Log::error('Checkout failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An unexpected server error occurred.'], 500);
        }
    }



    public function success(Request $request)
    {
        return view('checkout.success');
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }
}

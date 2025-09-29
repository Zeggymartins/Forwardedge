<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Add item
    public function add(Request $request)
    {
        // Accept request from both guests and auth; return 401 if not authed
        if (!Auth::check()) {
            return response()->json(['status' => 'auth_required', 'message' => 'Please register or login to continue'], 401);
        }

        $data = $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $course = Course::findOrFail($data['course_id']);
        $price = $course->discount_price ?? $course->price ?? 0;
        $quantity = $data['quantity'] ?? 1;

        $item = CartItem::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->price = $price;
            $item->save();
        } else {
            $item = CartItem::create([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'price' => $price,
                'quantity' => $quantity
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Added to cart',
            'cart' => $this->buildCartPayload(Auth::id())
        ]);
    }

    // Remove single item
    public function remove(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'auth_required', 'message' => 'Please register or login to continue'], 401);
        }

        $request->validate(['course_id' => 'required|integer|exists:courses,id']);

        CartItem::where('user_id', Auth::id())->where('course_id', $request->course_id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Removed from cart',
            'cart' => $this->buildCartPayload(Auth::id())
        ]);
    }

    // Get cart
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your cart');
        }

        $cartItems = CartItem::with('course')->where('user_id', Auth::id())->get();

        return view('user.pages.cart', compact('cartItems'));
    }

    // ✅ JSON version for AJAX
    public function getCartJson()
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'auth_required', 'message' => 'Please login'], 401);
        }

        return response()->json([
            'status' => 'success',
            'cart' => $this->buildCartPayload(Auth::id())
        ]);
    }

    protected function buildCartPayload($userId)
    {
        $items = CartItem::with('course')->where('user_id', $userId)->get();

        return $items->map(function ($i) {
            return [
                'course_id' => $i->course_id,
                'name'      => $i->course->title,
                'price'     => (float) $i->price,
                'quantity'  => (int) $i->quantity,
                'image'     => $i->course->thumbnail
                    ? asset('storage/' . $i->course->thumbnail)
                    : asset('frontend/assets/images/course-placeholder.webp'),
                'slug'      => $i->course->slug
            ];
        })->values();
    }
    public function count()
    {
        if (!Auth::check()) {
            return response()->json(['cart_count' => 0]);
        }

        $count = CartItem::where('user_id', Auth::id())->count();

        return response()->json(['cart_count' => $count]);
    }
}






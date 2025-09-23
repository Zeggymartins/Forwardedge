<?php

namespace App\Http\Controllers;

use App\Models\WishlistItem;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function add(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'auth_required', 'message' => 'Please register or login to continue'], 401);
        }

        $data = $request->validate(['course_id' => 'required|integer|exists:courses,id']);

        $course = Course::findOrFail($data['course_id']);
        $price = $course->discount_price ?? $course->price ?? null;

        $exists = WishlistItem::where('user_id', Auth::id())->where('course_id', $course->id)->first();
        if (!$exists) {
            WishlistItem::create([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'price' => $price
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Added to wishlist',
            'wishlist' => $this->buildWishlistPayload(Auth::id())
        ]);
    }

    public function remove(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'auth_required', 'message' => 'Please register or login to continue'], 401);
        }

        $request->validate(['course_id' => 'required|integer|exists:Course,id']);

        WishlistItem::where('user_id', Auth::id())->where('course_id', $request->course_id)->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Removed from wishlist',
            'wishlist' => $this->buildWishlistPayload(Auth::id())
        ]);
    }
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your cart');
        }

        $wishlistItems = WishlistItem::with('course')->where('user_id', Auth::id())->get();

        return view('user.pages.wishlist', compact('wishlistItems'));
    }

    // ✅ JSON version for AJAX
    public function getWishlistJson()
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'auth_required', 'message' => 'Please login'], 401);
        }

        return response()->json([
            'status' => 'success',
            'wishlist' => $this->buildWishlistPayload(Auth::id())
        ]);
    }
    protected function buildWishlistPayload($userId)
    {
        $items = WishlistItem::with('course')->where('user_id', $userId)->get();

        return $items->map(function ($i) {
            return [
                'course_id' => $i->course_id,
                'name' => $i->course->title,
                'price' => $i->price ? (float)$i->price : null,
                'image' => $i->course->thumbnail ? asset('storage/' . $i->course->thumbnail) : asset('frontend/assets/images/service-3.webp'),
                'slug' => $i->course->slug
            ];
        })->values();
    }

    public function count()
    {
        if (!Auth::check()) {
            return response()->json(['wishlist_count' => 0]);
        }

        $count = WishlistItem::where('user_id', Auth::id())->count();

        return response()->json(['wishlist_count' => $count]);
    }
}
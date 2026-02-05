<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Course;
use App\Models\CourseContent;
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
            'course_content_id' => 'nullable|integer|exists:course_contents,id',
            'quantity' => 'nullable|integer|min:1'
        ]);

        $course = Course::findOrFail($data['course_id']);

        // Block external courses from being added to cart
        if ($course->isExternal()) {
            return response()->json([
                'error' => 'This course is available on ' . ($course->external_platform_name ?? 'an external platform') . '. Please purchase directly from the platform.',
                'external_url' => $course->external_course_url
            ], 422);
        }

        $contentId = $data['course_content_id'] ?? null;

        if ($contentId) {
            $courseContent = CourseContent::where('id', $contentId)
                ->where('course_id', $course->id)
                ->first();

            if (!$courseContent) {
                return response()->json(['error' => 'The selected module does not belong to this course.'], 422);
            }
        } else {
            $courseContent = CourseContent::where('course_id', $course->id)
                ->orderByRaw('COALESCE(discount_price, price, 0) ASC')
                ->first();
            $contentId = $courseContent?->id;
        }

        $price = $courseContent ? ($courseContent->discount_price ?? $courseContent->price ?? 0) : 0;
        $quantity = $data['quantity'] ?? 1;

        $item = CartItem::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->when($contentId, fn($query) => $query->where('course_content_id', $contentId))
            ->when(!$contentId, fn($query) => $query->whereNull('course_content_id'))
            ->first();

        if ($item) {
            $item->quantity += $quantity;
            $item->price = $price;
            $item->save();
        } else {
            $item = CartItem::create([
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'course_content_id' => $contentId,
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

        $data = $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'course_content_id' => 'nullable|integer|exists:course_contents,id',
        ]);

        CartItem::where('user_id', Auth::id())
            ->where('course_id', $data['course_id'])
            ->when($data['course_content_id'] ?? null, fn($query) => $query->where('course_content_id', $data['course_content_id']))
            ->when(!($data['course_content_id'] ?? null), fn($query) => $query->whereNull('course_content_id'))
            ->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Removed from cart',
            'cart' => $this->buildCartPayload(Auth::id())
        ]);
    }

    // Get cart
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'auth_required',
                'message' => 'Please login to continue'
            ], 401);
        }



        $cartItems = CartItem::with(['course', 'courseContent'])->where('user_id', Auth::id())->get();

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
        $items = CartItem::with(['course', 'courseContent'])->where('user_id', $userId)->get();

        return $items->map(function ($i) {
            return [
                'course_id' => $i->course_id,
                'course_content_id' => $i->course_content_id,
                'name'      => $i->course->title,
                'module'    => $i->courseContent?->title,
                'price'     => (float) $i->price,
                'quantity'  => (int) $i->quantity,
                'image'     => $i->course->thumbnail
                    ? asset('storage/' . $i->course->thumbnail)
                    : asset('frontend/assets/images/course-placeholder.webp'),
                'slug'      => $i->course->slug,
                'url'       => $i->courseContent && $i->course
                    ? route('shop.details', ['slug' => $i->course->slug, 'content' => $i->courseContent->id])
                    : route('course.show', $i->course->slug),
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

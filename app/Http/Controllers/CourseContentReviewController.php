<?php

namespace App\Http\Controllers;

use App\Models\CourseContent;
use App\Models\CourseContentReview;
use Illuminate\Http\Request;

class CourseContentReviewController extends Controller
{
    public function store(Request $request, CourseContent $content)
    {
        if (!$request->user()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Please login to leave a review.',
            ], 401);
        }

        if (!$content->course || $content->course->status !== 'published') {
            abort(404);
        }

        $data = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:2000'],
        ]);

        $review = CourseContentReview::updateOrCreate(
            [
                'course_content_id' => $content->id,
                'user_id'           => $request->user()->id,
            ],
            [
                'rating'  => $data['rating'],
                'comment' => $data['comment'],
                'status'  => 'published',
            ]
        );

        $review->load('user');

        return response()->json([
            'status'  => 'success',
            'message' => 'Review saved successfully.',
            'review'  => [
                'id'        => $review->id,
                'name'      => $review->user->name,
                'rating'    => $review->rating,
                'comment'   => $review->comment,
                'created_at'=> $review->created_at?->diffForHumans(),
            ],
        ]);
    }
}

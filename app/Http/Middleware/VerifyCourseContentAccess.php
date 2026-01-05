<?php

namespace App\Http\Middleware;

use App\Models\CourseContent;
use App\Models\CourseContentAccessLog;
use App\Models\OrderItem;
use App\Models\Enrollment;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyCourseContentAccess
{
    /**
     * Handle an incoming request.
     * Verifies that the authenticated user has purchased or enrolled in the course content.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // User must be authenticated
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Please login to access this content'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Please login to access course content');
        }

        $user = Auth::user();
        $contentId = $request->route('content');

        // Find the course content
        $content = CourseContent::find($contentId);
        if (!$content) {
            abort(404, 'Content not found');
        }

        // Check if user has access via purchase (OrderItem)
        $hasPurchased = OrderItem::where('course_content_id', $content->id)
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('status', 'paid');
            })
            ->exists();

        if ($hasPurchased) {
            // Log this access attempt
            $this->logAccess($content->id, $user->email);
            return $next($request);
        }

        // Check if user has access via course enrollment
        if ($content->course_id) {
            $hasEnrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $content->course_id)
                ->exists();

            if ($hasEnrollment) {
                // Log this access attempt
                $this->logAccess($content->id, $user->email);
                return $next($request);
            }
        }

        // Check if user was explicitly granted access via Google Drive
        $hasExplicitAccess = CourseContentAccessLog::where('course_content_id', $content->id)
            ->where('email', $user->email)
            ->where('status', 'granted')
            ->exists();

        if ($hasExplicitAccess) {
            // Log this access attempt
            $this->logAccess($content->id, $user->email);
            return $next($request);
        }

        // No access found
        abort(403, 'You do not have access to this content. Please purchase the course or module first.');
    }

    /**
     * Log the access attempt to track usage
     *
     * @param int $contentId
     * @param string $email
     * @return void
     */
    protected function logAccess(int $contentId, string $email): void
    {
        // Update last_accessed_at and increment access_count
        $log = CourseContentAccessLog::where('course_content_id', $contentId)
            ->where('email', $email)
            ->where('status', 'granted')
            ->first();

        if ($log) {
            $log->update([
                'last_accessed_at' => now(),
                'access_count' => $log->access_count + 1,
            ]);
        }
    }
}

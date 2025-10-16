<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ScholarshipApplication;
use Illuminate\Http\Request;

class AdminEnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments.
     */
    public function index()
    {
        // eager-load related models (user, course, courseSchedule+course)
        $enrollments = Enrollment::with(['user', 'course', 'courseSchedule.course'])
            ->latest()
            ->paginate(10);

        return view('admin.pages.enrollment', compact('enrollments'));
    }

    /**
     * Display a single enrollment (if you want JSON or API style).
     */
    public function show($id)
    {
        $enrollment = Enrollment::with(['user', 'course', 'courseSchedule.course'])->findOrFail($id);

        return response()->json($enrollment);
    }

    public function approve(ScholarshipApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('warning', 'Already processed');
        }

        // Create enrollment for free schedule
        Enrollment::create([
            'course_id'         => $application->course_id,
            'course_schedule_id' => $application->course_schedule_id,
            'user_id'           => $application->user_id,
            'status'            => 'active', // or 'confirmed'
            'price_paid'        => 0,
            // add other fields as your Enrollment requires
        ]);

        $application->update([
            'status'      => 'approved',
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Application approved & enrollment created.');
    }

    public function reject(Request $request, ScholarshipApplication $application)
    {
        if ($application->status !== 'pending') {
            return back()->with('warning', 'Already processed');
        }

        $application->update([
            'status'      => 'rejected',
            'rejected_at' => now(),
            'admin_notes' => $request->input('notes'),
        ]);

        return back()->with('success', 'Application rejected.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ScholarshipStatusMail;
use App\Models\Enrollment;
use App\Models\OrderItem;
use App\Models\ScholarshipApplication;
use App\Services\ScholarshipApplicationManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        $moduleEnrollments = OrderItem::with(['order.user', 'course', 'courseContent'])
            ->whereNotNull('course_content_id')
            ->latest()
            ->paginate(10, ['*'], 'modules_page');

        return view('admin.pages.enrollment', compact('enrollments', 'moduleEnrollments'));
    }

    /**
     * Display a single enrollment (if you want JSON or API style).
     */
    public function show($id)
    {
        $enrollment = Enrollment::with(['user', 'course', 'courseSchedule.course'])->findOrFail($id);

        return response()->json($enrollment);
    }

    public function applications()
    {
        $applications = ScholarshipApplication::with(['user', 'course', 'schedule.course'])
            ->latest()
            ->paginate(20);

        return view('admin.pages.courses.scholarship.applications', compact('applications'));
    }

    public function approve(ScholarshipApplication $application, ScholarshipApplicationManager $manager)
    {
        if ($application->status === 'approved') {
            return back()->with('warning', 'Application already approved.');
        }

        $manager->approve($application);

        return back()->with('success', 'Application approved & enrollment created.');
    }

    public function reject(Request $request, ScholarshipApplication $application)
    {
        if ($application->status === 'rejected') {
            return back()->with('warning', 'Application already rejected.');
        }

        $data = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $application->update([
            'status'      => 'rejected',
            'rejected_at' => now(),
            'admin_notes' => $data['notes'] ?? null,
        ]);

        $application->refresh();

        if ($application->user?->email) {
            Mail::to($application->user->email)->send(
                new ScholarshipStatusMail($application, 'rejected', $data['notes'] ?? null)
            );
        }

        return back()->with('success', 'Application rejected.');
    }
}

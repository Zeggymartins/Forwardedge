<?php

namespace App\Http\Controllers;

use App\Models\CourseSchedule;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EnrollmentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'schedule_id'  => 'required|exists:course_schedules,id',
            'payment_plan' => 'required|in:full,partial',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'auth_required', 'message' => 'Please login first'], 401);
        }

        $schedule = CourseSchedule::findOrFail($data['schedule_id']);

        // ❌ Block enrollment if bootcamp has started
        if (Carbon::now()->gte($schedule->start_date)) {
            return response()->json([
                'status' => 'closed',
                'message' => 'Enrollment closed. This bootcamp has already started.',
            ], 403);
        }

        $total = $schedule->price;
        $initialPayment = $total;
        $balance = 0;

        if ($data['payment_plan'] === 'partial') {
            $initialPayment = round($total * 0.7, 2); // 70% upfront
            $balance = $total - $initialPayment;
        }

        // Create Enrollment
        $enrollment = Enrollment::create([
            'user_id'           => $user->id,
            'course_schedule_id' => $schedule->id,
            'payment_plan'      => $data['payment_plan'],
            'total_amount'      => $total,
            'balance'           => $balance,
            'status'            => 'pending', // will change to active after payment verification
        ]);

        // Record initial payment (gateway integration will update `status`)
        Payment::create([
            'enrollment_id' => $enrollment->id,
            'amount'        => $initialPayment,
            'status'        => 'pending',
            'method'        => 'gateway', // e.g. Paystack, Stripe, etc.
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Enrollment initiated. Redirecting to payment gateway...',
            'enrollment' => $enrollment,
            'payment' => [
                'amount' => $initialPayment,
                'status' => 'pending',
            ]
        ]);
    }
    public function pricingPage($scheduleId)
    {
        $schedule = CourseSchedule::findOrFail($scheduleId);
        return view('user.pages.pricing', compact('schedule'));
    }
}

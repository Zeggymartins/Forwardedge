<?php

namespace App\Http\Controllers;

use App\Models\CourseSchedule;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


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
            return response()->json(['error' => 'Please login first'], 401);
        }

        $schedule = CourseSchedule::findOrFail($data['schedule_id']);

        $total = $schedule->price;
        $initialPayment = $data['payment_plan'] === 'partial'
            ? round($total * 0.7, 2)
            : $total;

        // ✅ Create Payment intent (no enrollment yet)
        $payment = Payment::create([
            'user_id'       => $user->id,
            'payable_id'    => $schedule->id, // temp, enrollment later
            'payable_type'  => CourseSchedule::class,
            'amount'        => $initialPayment,
            'status'        => 'pending',
            'method'        => 'paystack',
            'reference'     => Str::uuid(), // safer than uniqid
            'currency'      => 'NGN',
            'metadata'      => [
                'schedule_id'   => $schedule->id,
                'payment_plan'  => $data['payment_plan'],
                'total'         => $total,
                'initial'       => $initialPayment,
                'user_id'       => $user->id,
            ],
        ]);

        // ✅ Initialize Paystack payment
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.paystack.secret'),
            'Accept'        => 'application/json',
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email'        => $user->email,
            'amount'       => $initialPayment * 100, // in kobo
            'reference'    => $payment->reference,
            'callback_url' => route('payment.callback'),
        ]);

        if (!$response->successful()) {
            Log::error('Paystack initialization failed', ['response' => $response->body()]);
            return response()->json(['error' => 'Payment initialization failed.'], 500);
        }

        $resData = $response->json();

        if (!isset($resData['data']['authorization_url'])) {
            return response()->json(['error' => 'Invalid Paystack response'], 500);
        }

        return response()->json([
            'authorization_url' => $resData['data']['authorization_url'],
            'reference'         => $payment->reference,
        ]);
    }


    public function pricingPage(CourseSchedule $schedule)
    {
        return view('user.pages.pricing', compact('schedule'));
    }
}

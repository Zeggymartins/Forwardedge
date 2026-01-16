<?php

namespace App\Http\Controllers;

use App\Models\CourseSchedule;
use App\Models\Enrollment;
use App\Models\Page;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class EnrollmentController extends Controller
{

    public function pricingPage(Request $request)
    {
        $pageId  = (int) $request->query('page');
        $blockId = (int) $request->query('block');
        $planIdx = (int) $request->query('plan', -1);

        if (!$pageId || !$blockId || $planIdx < 0) {
            abort(404, 'Missing pricing context.');
        }

        // Load the exact block on that page
        $page = Page::with(['blocks' => function ($q) use ($blockId) {
            $q->where('id', $blockId);
        }])->findOrFail($pageId);

        $block = $page->blocks->first();
        if (!$block) {
            abort(404, 'Pricing block not found.');
        }

        // Decode block data safely
        $data = is_array($block->data) ? $block->data : (json_decode($block->data ?? '[]', true) ?: []);
        $plans = $data['plans'] ?? [];
        if (!isset($plans[$planIdx])) {
            abort(404, 'Selected plan not found.');
        }

        $p = $plans[$planIdx];

        $courseId = (int) ($p['course_id'] ?? 0);
        $scheduleId = null;
        if ($courseId > 0) {
            $schedule = CourseSchedule::where('course_id', $courseId)
                ->upcoming()
                ->orderBy('start_date')
                ->first();

            if (!$schedule) {
                $schedule = CourseSchedule::where('course_id', $courseId)
                    ->orderBy('start_date')
                    ->first();
            }

            $scheduleId = $schedule?->id;
        }

        // Normalize numeric prices
        $ngnRaw = preg_replace('/[^\d.]/', '', (string)($p['price_naira'] ?? '0'));
        $usdRaw = preg_replace('/[^\d.]/', '', (string)($p['price_usd']   ?? ''));
        $priceNgn = (float) ($ngnRaw !== '' ? $ngnRaw : 0);
        $priceUsd = $usdRaw !== '' ? (float) $usdRaw : null;

        // Build the plan payload for the view + later POST (but store it server-side)
        $plan = [
            'title'        => $p['title'] ?? 'Plan',
            'subtitle'     => $p['subtitle'] ?? null,
            'features'     => is_array($p['features'] ?? null) ? $p['features'] : [],
            'period'       => $p['period'] ?? '/one-time',
            'price_naira'  => $priceNgn,
            'price_usd'    => $priceUsd,
            'page_id'      => $pageId,
            'block_id'     => $blockId,
            'plan_index'   => $planIdx,
            'return_url'   => route('page.show', $page->slug), // back to the same dynamic page
            'course_id'    => $courseId ?: null,
            'schedule_id'  => $scheduleId,
        ];

        session(['enroll.selected_plan' => $plan]);

        return view('user.pages.pricing', compact('plan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_type' => 'required|in:full,partial', // "full" | "partial"
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Please login first'], 401);
        }

        $plan = session('enroll.selected_plan');
        if (!$plan || !is_array($plan)) {
            return response()->json(['error' => 'Your pricing context has expired. Please re-open the pricing page.'], 422);
        }

        $scheduleId = (int) ($plan['schedule_id'] ?? 0);
        if ($scheduleId <= 0 && !empty($plan['course_id'])) {
            $schedule = CourseSchedule::where('course_id', (int) $plan['course_id'])
                ->upcoming()
                ->orderBy('start_date')
                ->first();
            $scheduleId = $schedule?->id ?? 0;
        }

        if ($scheduleId <= 0) {
            return response()->json(['error' => 'Enrollment schedule not found. Please contact support.'], 422);
        }

        $priceNgn = (float) ($plan['price_naira'] ?? 0);
        if ($priceNgn <= 0) {
            return response()->json(['error' => 'Invalid plan price.'], 422);
        }

        $planType   = $data['plan_type'];
        $totalNgn   = $priceNgn;
        $initialNgn = $planType === 'partial' ? round($totalNgn * 0.7, 2) : $totalNgn;

        $payment = Payment::create([
            'user_id'       => $user->id,
            'payable_id'    => $scheduleId,
            'payable_type'  => CourseSchedule::class,
            'amount'        => $initialNgn,           // NGN amount
            'status'        => 'pending',
            'method'        => 'paystack',
            'reference'     => Str::uuid()->toString(),
            'currency'      => 'NGN',
            'metadata'      => [
                'payment_plan' => $planType,
                'total'        => $totalNgn,
                'plan_type'     => $planType,
                'page_id'       => $plan['page_id'] ?? null,
                'block_id'      => $plan['block_id'] ?? null,
                'plan_index'    => $plan['plan_index'] ?? null,
                'plan_title'    => $plan['title'] ?? null,
                'plan_subtitle' => $plan['subtitle'] ?? null,
                'plan_features' => $plan['features'] ?? [],
                'period'        => $plan['period'] ?? null,
                'price_naira'   => $plan['price_naira'] ?? null,
                'price_usd'     => $plan['price_usd'] ?? null,
                'total_ngn'     => $totalNgn,
                'initial_ngn'   => $initialNgn,
                'user_id'       => $user->id,
                'schedule_id'   => $scheduleId,
                'return_url'    => $plan['return_url'] ?? url('/'),
            ],
        ]);

        // Initialize Paystack
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.paystack.secret'),
            'Accept'        => 'application/json',
        ])->post('https://api.paystack.co/transaction/initialize', [
            'email'        => $user->email,
            'amount'       => (int) round($initialNgn * 100), // kobo
            'reference'    => $payment->reference,
            'callback_url' => route('payment.callback'),
        ]);

        if (!$response->successful()) {
            Log::error('Paystack initialization failed', ['response' => $response->body()]);
            return response()->json(['error' => 'Payment initialization failed.'], 500);
        }

        $res = $response->json();
        if (!isset($res['data']['authorization_url'])) {
            return response()->json(['error' => 'Invalid Paystack response'], 500);
        }

        return response()->json([
            'authorization_url' => $res['data']['authorization_url'],
            'reference'         => $payment->reference,
        ]);
    }
}

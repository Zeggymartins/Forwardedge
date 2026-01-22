<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ScholarshipStatusMail;
use App\Models\Enrollment;
use App\Models\OrderItem;
use App\Models\ScholarshipApplication;
use App\Services\ScholarshipApplicationManager;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class AdminEnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments.
     */
    public function index(Request $request)
    {
        // Get filter inputs
        $search = trim((string) $request->input('search', ''));
        $enrollmentId = trim((string) $request->input('enrollment_id', ''));
        $country = $request->input('country');
        $status = $request->input('status');
        $verificationStatus = $request->input('verification_status');
        $perPage = (int) $request->input('per_page', 20);

        $perPageOptions = [10, 20, 50, 100];
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 20;
        }

        $allowedStatuses = ['active', 'pending', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = null;
        }

        $allowedVerificationStatuses = ['verified', 'pending', 'rejected', 'unverified'];
        if (!in_array($verificationStatus, $allowedVerificationStatuses, true)) {
            $verificationStatus = null;
        }

        // Get all unique countries from users for filter dropdown
        $allCountries = \App\Models\User::whereNotNull('nationality')
            ->where('nationality', '!=', '')
            ->distinct()
            ->orderBy('nationality')
            ->pluck('nationality');

        // Build query with filters
        $enrollments = Enrollment::with(['user', 'course', 'courseSchedule.course'])
            ->when($search, function ($q) use ($search) {
                $term = '%' . $search . '%';
                $q->whereHas('user', function ($userQuery) use ($term) {
                    $userQuery->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when($enrollmentId, function ($q) use ($enrollmentId) {
                $q->whereHas('user', function ($userQuery) use ($enrollmentId) {
                    $userQuery->where('enrollment_id', 'like', '%' . $enrollmentId . '%');
                });
            })
            ->when($country, function ($q) use ($country) {
                $q->whereHas('user', function ($userQuery) use ($country) {
                    $userQuery->where('nationality', $country);
                });
            })
            ->when($verificationStatus, function ($q) use ($verificationStatus) {
                $q->whereHas('user', function ($userQuery) use ($verificationStatus) {
                    if ($verificationStatus === 'unverified') {
                        $userQuery->whereNull('verification_status')
                            ->orWhere('verification_status', 'unverified');
                    } else {
                        $userQuery->where('verification_status', $verificationStatus);
                    }
                });
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $moduleEnrollments = OrderItem::with(['order.user', 'course', 'courseContent'])
            ->whereNotNull('course_content_id')
            ->latest()
            ->paginate(10, ['*'], 'modules_page')
            ->withQueryString();

        return view('admin.pages.enrollment', compact(
            'enrollments',
            'moduleEnrollments',
            'search',
            'enrollmentId',
            'country',
            'status',
            'verificationStatus',
            'perPage',
            'perPageOptions',
            'allCountries',
            'allowedStatuses',
            'allowedVerificationStatuses'
        ));
    }

    public function exportExcel()
    {
        $rows = $this->buildExportRows();
        $filename = 'enrollments-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_keys($rows[0] ?? []));
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $rows = $this->buildExportRows();
        $html = view('admin.pages.enrollment-export-pdf', [
            'rows' => $rows,
            'generatedAt' => now(),
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'enrollments-' . now()->format('Ymd-His') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function buildExportRows(): array
    {
        $enrollments = Enrollment::with(['user', 'course', 'courseSchedule.course'])
            ->latest()
            ->get();

        return $enrollments->map(function (Enrollment $enrollment) {
            $user = $enrollment->user;
            $course = $enrollment->course ?: $enrollment->courseSchedule?->course;
            $courseType = $enrollment->course ? 'Self-paced' : ($enrollment->courseSchedule ? 'Bootcamp' : '—');

            return [
                'Enrollment ID' => $user?->enrollment_id ?? '',
                'Name' => $user?->name ?? '',
                'Email' => $user?->email ?? '',
                'Course' => $course?->title ?? '',
                'Course Type' => $courseType,
                'Plan' => $enrollment->payment_plan,
                'Total' => $enrollment->total_amount,
                'Balance' => $enrollment->balance,
                'Status' => $enrollment->status,
                'Verification Status' => $user?->verification_status ?? 'unverified',
                'Created At' => optional($enrollment->created_at)->format('Y-m-d H:i:s'),
            ];
        })->values()->all();
    }

    /**
     * Display a single enrollment (if you want JSON or API style).
     */
    public function show($id)
    {
        $enrollment = Enrollment::with(['user', 'course', 'courseSchedule.course'])->findOrFail($id);

        return response()->json($enrollment);
    }

    public function applications(Request $request)
    {
        $perPageOptions = [10, 20, 50, 100];
        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 20;
        }

        $nameEmail = trim((string) $request->input('name_email', ''));
        if ($nameEmail === '') {
            $nameEmail = null;
        }

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $fromDate = null;
        $toDate = null;

        if ($dateFrom) {
            try {
                $fromDate = Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay();
            } catch (\Throwable $e) {
                $dateFrom = null;
            }
        }

        if ($dateTo) {
            try {
                $toDate = Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay();
            } catch (\Throwable $e) {
                $dateTo = null;
            }
        }

        $scoreMin = $request->input('score_min');
        $scoreMax = $request->input('score_max');
        $scoreSort = $request->input('score_sort');
        $status = $request->input('status');
        $allowedStatuses = ['pending', 'approved', 'rejected'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = null;
        }
        $discoveryChannel = $request->input('discovery_channel');
        $allowedDiscovery = array_keys(config('scholarship.form_options.discovery_channels', []));
        if (!in_array($discoveryChannel, $allowedDiscovery, true)) {
            $discoveryChannel = null;
        }

        // Get country filter
        $country = $request->input('country');

        // Get all unique countries from applications for filter dropdown
        $allLocations = ScholarshipApplication::select('form_data')
            ->get()
            ->pluck('form_data.personal.location')
            ->filter();

        // Extract clean country names and map them to original locations
        $locationMap = [];
        foreach ($allLocations as $location) {
            $cleanCountry = $this->extractCountryFromLocation($location);
            if (!isset($locationMap[$cleanCountry])) {
                $locationMap[$cleanCountry] = [];
            }
            $locationMap[$cleanCountry][] = $location;
        }

        // Get unique country names sorted alphabetically
        $allCountries = collect(array_keys($locationMap))->sort()->values();

        $applications = ScholarshipApplication::with(['user', 'course', 'schedule.course'])
            ->when($nameEmail, function ($q) use ($nameEmail) {
                $term = '%' . $nameEmail . '%';
                $q->where(function ($query) use ($term) {
                    $query->whereHas('user', function ($userQuery) use ($term) {
                        $userQuery->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term);
                    })
                        ->orWhere('form_data->contact->name', 'like', $term)
                        ->orWhere('form_data->contact->email', 'like', $term)
                        ->orWhere('form_data->personal->full_name', 'like', $term)
                        ->orWhere('form_data->personal->email', 'like', $term);
                });
            })
            ->when($fromDate, fn ($q) => $q->where('created_at', '>=', $fromDate))
            ->when($toDate, fn ($q) => $q->where('created_at', '<=', $toDate))
            ->when($scoreMin !== null && $scoreMin !== '', fn ($q) => $q->where('score', '>=', (int) $scoreMin))
            ->when($scoreMax !== null && $scoreMax !== '', fn ($q) => $q->where('score', '<=', (int) $scoreMax))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($discoveryChannel, fn ($q) => $q->where('form_data->attitude->discovery_channel', $discoveryChannel))
            ->when($country, function ($q) use ($country, $locationMap) {
                // Get all original location strings that map to this country
                $matchingLocations = $locationMap[$country] ?? [];
                if (!empty($matchingLocations)) {
                    $q->whereIn('form_data->personal->location', $matchingLocations);
                }
            })
            ->when(in_array($scoreSort, ['asc', 'desc'], true), function ($q) use ($scoreSort) {
                $q->orderBy('score', $scoreSort)->orderBy('created_at', 'desc');
            }, fn ($q) => $q->latest())
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.pages.courses.scholarship.applications', [
            'applications' => $applications,
            'perPage' => $perPage,
            'perPageOptions' => $perPageOptions,
            'scoreMin' => $scoreMin,
            'scoreMax' => $scoreMax,
            'scoreSort' => $scoreSort,
            'status' => $status,
            'statusOptions' => $allowedStatuses,
            'nameEmail' => $nameEmail,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'discoveryChannel' => $discoveryChannel,
            'country' => $country,
            'allCountries' => $allCountries,
        ]);
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
            try {
                Mail::to($application->user->email)->queue(
                    new ScholarshipStatusMail($application, 'rejected', $data['notes'] ?? null)
                );
            } catch (\Exception $e) {
                \Log::error('Failed to queue rejection email', ['application_id' => $application->id, 'error' => $e->getMessage()]);
            }
        }

        return back()->with('success', 'Application rejected.');
    }

    /**
     * Extract clean country name from location string
     *
     * Examples:
     * "Yaba, Lagos, Nigeria" -> "Nigeria"
     * "Lagos, Nigeria" -> "Nigeria"
     * "Kigali, Rwanda" -> "Rwanda"
     * "Karachi, Pakistan" -> "Pakistan"
     * "Nigeria" -> "Nigeria"
     * "Yaba" -> "Yaba" (if no other parts)
     */
    protected function extractCountryFromLocation(string $location): string
    {
        // Clean and normalize location strings
        $location = trim($location);

        // Split by common delimiters (comma, hyphen, pipe)
        $parts = preg_split('/[,\-|]/', $location);
        $parts = array_map('trim', $parts);
        $parts = array_filter($parts); // Remove empty parts

        // Get the last part (usually country) and trim
        $lastPart = trim(end($parts));

        // Common city/state names that should be skipped
        $cities = [
            'yaba', 'lagos', 'abuja', 'kano', 'ibadan', 'port harcourt',
            'benin city', 'enugu', 'kaduna', 'jos', 'calabar', 'warri',
            'onitsha', 'aba', 'ilorin', 'abeokuta', 'owerri', 'maiduguri'
        ];

        // If the last part is a known city and we have more parts, use second-to-last
        if (count($parts) > 1 && in_array(strtolower($lastPart), $cities)) {
            $secondLast = trim($parts[count($parts) - 2]);
            return ucwords(strtolower($secondLast));
        }

        // Return the cleaned country/location (capitalize properly)
        return ucwords(strtolower($lastPart));
    }
}

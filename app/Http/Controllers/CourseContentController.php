<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseContent;
use App\Models\CourseContentAccessLog;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CourseContentController extends Controller
{
    protected GoogleDriveService $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    /**
     * Show course content page for authenticated student
     */
    public function show(Course $course, Request $request)
    {
        $user = Auth::user();

        // Load course with contents
        $course->load('contents');

        // Get accessible content for this user
        $accessibleContentIds = $this->getAccessibleContentIds($user->id, $user->email, $course->id);

        // Filter contents that user has access to
        $accessibleContents = $course->contents->filter(function ($content) use ($accessibleContentIds) {
            return in_array($content->id, $accessibleContentIds);
        });

        // Gmail users: jump straight to Drive (unless list=1 is explicitly set).
        if ($this->isGoogleEmail($user->email) && !$request->boolean('list')) {
            $driveTarget = $accessibleContents->first(function ($content) {
                return !empty($content->drive_share_link);
            });

            if ($driveTarget) {
                return redirect()->route('student.content.view', $driveTarget->id);
            }
        }

        return view('student.course-content', [
            'course' => $course,
            'contents' => $accessibleContents,
        ]);
    }

    /**
     * View a specific course content
     * - Gmail users: Redirects to Google Drive (native experience)
     * - Non-Gmail users: Shows embedded viewer on our platform
     */
    public function view(CourseContent $content, Request $request)
    {
        // Middleware already verified access
        $user = Auth::user();

        // Log the access
        $this->logAccess($content->id, $user->email);

        // Check if user wants to force embedded view
        $forceEmbed = $request->query('embed', false);

        // PRIORITY 1: Gmail users with drive_share_link -> Redirect directly to Drive
        if (!$forceEmbed && $this->isGoogleEmail($user->email) && $content->drive_share_link) {
            return redirect()->away($content->drive_share_link);
        }

        // PRIORITY 2: If we have a drive_folder_id, try to stream from Drive API
        if ($content->drive_folder_id) {
            try {
                return $this->streamFromDrive($content, $user->email, $request);
            } catch (\Exception $e) {
                Log::error('Failed to stream content from Drive', [
                    'content_id' => $content->id,
                    'user_email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
                // Fall through to other options
            }
        }

        // PRIORITY 3: If we have a drive_share_link but no folder_id (for non-Gmail), show redirect page
        if ($content->drive_share_link) {
            return view('student.content-redirect', [
                'content' => $content,
                'driveUrl' => $content->drive_share_link,
                'userEmail' => $user->email,
            ]);
        }

        // PRIORITY 4: Handle local file content
        if ($content->file_path && file_exists(storage_path('app/' . $content->file_path))) {
            return response()->file(storage_path('app/' . $content->file_path));
        }

        // PRIORITY 5: No content available - show helpful message
        return view('student.content-unavailable', [
            'content' => $content,
            'course' => $content->course,
        ]);
    }

    /**
     * Direct redirect to Google Drive (for Gmail users)
     */
    public function redirect(CourseContent $content, Request $request)
    {
        // Middleware already verified access
        $user = Auth::user();

        if (!$content->drive_share_link) {
            return redirect()->route('student.content.view', $content->id)
                ->with('error', 'Direct Drive link not available. Using embedded viewer.');
        }

        // Log the access
        $this->logAccess($content->id, $user->email);

        // Show instruction page before redirecting
        return view('student.content-redirect', [
            'content' => $content,
            'driveUrl' => $content->drive_share_link,
            'userEmail' => $user->email,
        ]);
    }

    /**
     * Embed view for content (for iframe embedding)
     */
    public function embed(CourseContent $content, Request $request)
    {
        $user = Auth::user();

        return view('student.content-embed', [
            'content' => $content,
            'viewUrl' => route('student.content.view', $content),
        ]);
    }

    /**
     * Check if an email is a Google email (Gmail or Google Workspace)
     */
    protected function isGoogleEmail(string $email): bool
    {
        $domain = strtolower(substr(strrchr($email, "@"), 1));

        // Common Google domains
        $googleDomains = ['gmail.com', 'googlemail.com'];

        if (in_array($domain, $googleDomains)) {
            return true;
        }

        if (empty($domain)) {
            return false;
        }

        if (!config('services.google_workspace.mx_check', true)) {
            return false;
        }

        $cacheMinutes = (int) config('services.google_workspace.cache_minutes', 10080);

        return Cache::remember("google-workspace-mx:{$domain}", now()->addMinutes($cacheMinutes), function () use ($domain) {
            if (!function_exists('dns_get_record')) {
                return false;
            }

            $records = dns_get_record($domain, DNS_MX);
            if (!$records) {
                return false;
            }

            foreach ($records as $record) {
                $target = strtolower(rtrim($record['target'] ?? '', '.'));
                if ($target === '') {
                    continue;
                }
                if (str_contains($target, 'google.com') || str_contains($target, 'googlemail.com')) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Log access attempt for tracking
     */
    protected function logAccess(int $contentId, string $email): void
    {
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

    /**
     * Get list of content IDs accessible to a user
     */
    protected function getAccessibleContentIds(int $userId, string $email, ?int $courseId = null): array
    {
        $accessibleIds = [];

        // Get content purchased directly via OrderItem
        $purchasedContentIds = \App\Models\OrderItem::whereNotNull('course_content_id')
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->where('status', 'paid');
            })
            ->pluck('course_content_id')
            ->toArray();

        $accessibleIds = array_merge($accessibleIds, $purchasedContentIds);

        // Get content accessible via course enrollment
        if ($courseId) {
            $hasEnrollment = \App\Models\Enrollment::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->exists();

            if ($hasEnrollment) {
                $enrollmentContentIds = CourseContent::where('course_id', $courseId)
                    ->pluck('id')
                    ->toArray();

                $accessibleIds = array_merge($accessibleIds, $enrollmentContentIds);
            }
        }

        // Get content with explicit Drive access granted
        $grantedContentIds = CourseContentAccessLog::where('email', $email)
            ->where('status', 'granted')
            ->pluck('course_content_id')
            ->toArray();

        $accessibleIds = array_merge($accessibleIds, $grantedContentIds);

        return array_unique($accessibleIds);
    }

    /**
     * Stream content from Google Drive
     */
    protected function streamFromDrive(CourseContent $content, string $userEmail, Request $request)
    {
        if (!$this->driveService->isConfigured()) {
            abort(500, 'Google Drive is not configured');
        }

        // Get Drive service
        $drive = $this->driveService->getDriveService();
        if (!$drive) {
            abort(500, 'Unable to connect to Google Drive');
        }

        $folderId = $content->drive_folder_id;

        try {
            // List files in the folder
            $response = $drive->files->listFiles([
                'q' => "'{$folderId}' in parents and trashed=false",
                'orderBy' => 'name',
                'fields' => 'files(id, name, mimeType, size, webViewLink)',
            ]);

            $files = $response->getFiles();

            if (empty($files)) {
                abort(404, 'No files found in this content folder');
            }

            // For now, return a view with file listing
            // In production, you might want to stream specific files or show a file browser
            return view('student.content-viewer', [
                'content' => $content,
                'files' => $files,
                'folderId' => $folderId,
            ]);

        } catch (\Google_Service_Exception $e) {
            Log::error('Google Drive API error', [
                'content_id' => $content->id,
                'folder_id' => $folderId,
                'error' => $e->getMessage(),
            ]);

            abort(403, 'Unable to access content. Your access may have been revoked.');
        }
    }

    /**
     * Download a specific file from Google Drive folder
     * This should be heavily rate-limited
     */
    public function downloadFile(CourseContent $content, Request $request)
    {
        // Disabled - view-only access
        abort(403, 'Downloading is not allowed. Content is view-only.');

        // If you want to enable downloads later, uncomment and implement:
        /*
        $fileId = $request->input('file_id');
        if (!$fileId) {
            abort(400, 'File ID required');
        }

        $drive = $this->driveService->getDriveService();
        $file = $drive->files->get($fileId, ['fields' => 'name,mimeType']);

        $response = $drive->files->get($fileId, ['alt' => 'media']);

        return response()->streamDownload(function () use ($response) {
            echo $response->getBody()->getContents();
        }, $file->name, [
            'Content-Type' => $file->mimeType,
        ]);
        */
    }
}

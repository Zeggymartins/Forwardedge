<?php

namespace App\Services;

use App\Models\Orders;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CourseBundleService
{
    /**
     * Create a ZIP bundle of all course contents for an order.
     * Uses temporary directory to avoid storage bloat.
     *
     * @param Orders $order
     * @return string|null Full path to the ZIP or null on failure
     */
    public static function createZip(Orders $order): ?string
    {
        $zip = new ZipArchive();
        
        // Use temporary directory for better cleanup
        $tempDir = sys_get_temp_dir();
        $zipFilename = "order_{$order->id}_bundle_" . time() . ".zip";
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipFilename;

        Log::info("Creating course bundle ZIP", [
            'order_id' => $order->id,
            'zip_path' => $zipPath
        ]);

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            Log::error("Failed to create ZIP archive", [
                'order_id' => $order->id,
                'zip_path' => $zipPath
            ]);
            return null;
        }

        $filesAdded = 0;
        $errors = [];

        foreach ($order->orderItems as $item) {
            $course = $item->course;
            
            if (!$course) {
                Log::warning("Course not found for order item", ['item_id' => $item->id]);
                continue;
            }

            foreach ($course->contents as $content) {
                if (!$content->file_path) {
                    continue;
                }

                $filePath = storage_path("app/public/{$content->file_path}");

                if (!file_exists($filePath)) {
                    $errors[] = "File not found: {$content->file_path}";
                    Log::warning("Course content file missing", [
                        'content_id' => $content->id,
                        'file_path' => $content->file_path,
                        'course' => $course->name
                    ]);
                    continue;
                }

                // Determine file extension
                $extension = $content->type === 'text' 
                    ? 'docx' 
                    : pathinfo($content->file_path, PATHINFO_EXTENSION);

                // Sanitize filenames to prevent ZIP issues
                $safeCourseName = self::sanitizeFilename($course->name);
                $safeContentTitle = self::sanitizeFilename($content->title);
                $zipFileName = "{$safeCourseName}/{$safeContentTitle}.{$extension}";

                if ($zip->addFile($filePath, $zipFileName)) {
                    $filesAdded++;
                } else {
                    $errors[] = "Failed to add: {$zipFileName}";
                    Log::warning("Failed to add file to ZIP", [
                        'file_path' => $filePath,
                        'zip_name' => $zipFileName
                    ]);
                }
            }
        }

        $zip->close();

        if ($filesAdded === 0) {
            Log::error("No files added to ZIP bundle", [
                'order_id' => $order->id,
                'errors' => $errors
            ]);
            
            // Clean up empty ZIP
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
            
            return null;
        }

        $zipSize = filesize($zipPath);
        Log::info("Course ZIP created successfully", [
            'order_id' => $order->id,
            'zip_path' => $zipPath,
            'files_added' => $filesAdded,
            'size_mb' => round($zipSize / 1024 / 1024, 2),
            'errors' => $errors
        ]);

        return $zipPath;
    }

    /**
     * Clean up temporary ZIP file after email is sent
     *
     * @param string|null $zipPath
     * @return void
     */
    public static function cleanupZip(?string $zipPath): void
    {
        if (!$zipPath || !file_exists($zipPath)) {
            return;
        }

        try {
            unlink($zipPath);
            Log::info("Cleaned up temporary ZIP file", ['path' => $zipPath]);
        } catch (\Exception $e) {
            Log::warning("Failed to cleanup ZIP file", [
                'path' => $zipPath,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Sanitize filename for safe use in ZIP archives
     *
     * @param string $filename
     * @return string
     */
    private static function sanitizeFilename(string $filename): string
    {
        // Remove or replace characters that might cause issues
        $filename = preg_replace('/[^\w\s\-\.]/', '', $filename);
        $filename = preg_replace('/\s+/', '_', $filename);
        $filename = trim($filename, '_-');
        
        // Limit length
        return substr($filename, 0, 200);
    }

    /**
     * Get human-readable file size
     *
     * @param int $bytes
     * @return string
     */
    public static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
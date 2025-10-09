<?php

namespace App\Jobs;

use App\Mail\OrderPaid;
use App\Models\Orders;
use App\Services\CourseBundleService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $zipPath;

    public $tries = 3;
    public $timeout = 120; // 2 minutes for large attachments
    public $backoff = [60, 300]; // Retry after 1 min, then 5 min

    public function __construct(Orders $order, $zipPath)
    {
        $this->order = $order;
        $this->zipPath = $zipPath;
    }

    public function handle()
    {
        try {
            Log::info("Sending order email", [
                'order_id' => $this->order->id,
                'user_email' => $this->order->user->email,
                'has_zip' => !empty($this->zipPath)
            ]);

            Mail::to($this->order->user->email)
                ->send(new OrderPaid($this->order, $this->zipPath));

            Log::info("Order email sent successfully", [
                'order_id' => $this->order->id
            ]);

            // ✅ Clean up the temporary ZIP file after successful send
            if ($this->zipPath) {
                CourseBundleService::cleanupZip($this->zipPath);
            }
        } catch (\Exception $e) {
            Log::error("Failed to send order email", [
                'order_id' => $this->order->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // Clean up ZIP on final failure to avoid orphaned files
            if ($this->attempts() >= $this->tries && $this->zipPath) {
                CourseBundleService::cleanupZip($this->zipPath);
            }

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Handle job failure (after all retries exhausted)
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Order email job permanently failed", [
            'order_id' => $this->order->id,
            'user_email' => $this->order->user->email,
            'error' => $exception->getMessage()
        ]);

        // Final cleanup
        if ($this->zipPath) {
            CourseBundleService::cleanupZip($this->zipPath);
        }

        // Optional: Send admin notification or mark order for manual review
        // AdminNotification::orderEmailFailed($this->order);
    }
}

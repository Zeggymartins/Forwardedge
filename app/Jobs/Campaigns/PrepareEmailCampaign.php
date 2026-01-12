<?php

namespace App\Jobs\Campaigns;

use App\Jobs\Campaigns\SendEmailCampaign;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use App\Services\EmailTargetCollector;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PrepareEmailCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $campaignId,
        public bool $refreshRecipients = false
    ) {
    }

    public function handle(EmailTargetCollector $collector): void
    {
        $campaign = EmailCampaign::find($this->campaignId);

        if (!$campaign) {
            return;
        }

        if ($this->refreshRecipients) {
            $campaign->recipients()->delete();
            $campaign->forceFill([
                'sent_count' => 0,
                'total_count' => 0,
                'last_error' => null,
            ])->save();
        }

        $targets = $collector->all([
            'sources' => $campaign->audience_sources ?? [],
            'include' => $campaign->include_emails ?? [],
            'exclude' => $campaign->exclude_emails ?? [],
        ]);

        if ($targets->isEmpty()) {
            $campaign->forceFill([
                'status' => 'failed',
                'last_error' => 'No valid email addresses were found to send this campaign.',
                'sent_count' => 0,
                'total_count' => 0,
            ])->save();
            return;
        }

        $campaign->forceFill([
            'status' => 'sending',
            'last_error' => null,
            'sent_count' => $campaign->recipients()->where('status', 'sent')->count(),
        ])->save();

        $batchSize = 20;
        $batchWindowSeconds = 600;
        $dispatchIndex = 0;

        foreach ($targets->chunk(100) as $chunk) {
            foreach ($chunk as $target) {
                $recipient = EmailCampaignRecipient::firstOrNew([
                    'email_campaign_id' => $campaign->id,
                    'email' => $target['email'],
                ]);

                $wasSent = $recipient->exists && $recipient->status === 'sent' && !$this->refreshRecipients;

                $recipient->name = $target['name'] ?? null;

                if ($this->refreshRecipients || !$wasSent) {
                    $recipient->status = 'pending';
                    $recipient->error = null;
                    $recipient->sent_at = null;
                }

                $recipient->save();

                if ($this->refreshRecipients || !$wasSent) {
                    $batchIndex = intdiv($dispatchIndex, $batchSize);
                    $delay = $batchIndex * $batchWindowSeconds;
                    SendEmailCampaign::dispatch($campaign->id, $recipient->id)
                        ->delay(now()->addSeconds($delay));
                    $dispatchIndex++;
                }
            }
        }

        $campaign->forceFill([
            'total_count' => $campaign->recipients()->count(),
        ])->save();
    }
}

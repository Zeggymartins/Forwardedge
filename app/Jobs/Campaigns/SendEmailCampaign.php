<?php

namespace App\Jobs\Campaigns;

use App\Mail\EmailCampaignMail;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendEmailCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $campaignId,
        public int $recipientId
    ) {
    }

    public function handle(): void
    {
        $campaign = EmailCampaign::find($this->campaignId);
        $recipient = EmailCampaignRecipient::find($this->recipientId);

        if (!$campaign || !$recipient) {
            return;
        }

        if ($recipient->status === 'sent') {
            return;
        }

        if (!filter_var($recipient->email, FILTER_VALIDATE_EMAIL)) {
            $recipient->update([
                'status' => 'skipped',
                'error' => 'Invalid email format',
            ]);
            $campaign->refreshProgress();
            return;
        }

        try {
            Mail::to($recipient->email)->send(
                new EmailCampaignMail($campaign, $recipient->name)
            );

            $recipient->update([
                'status' => 'sent',
                'error' => null,
                'sent_at' => now(),
            ]);

            $campaign->increment('sent_count');
            $campaign->refreshProgress();
        } catch (Throwable $exception) {
            $recipient->update([
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            $campaign->forceFill([
                'status' => 'failed',
                'last_error' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }
    }
}

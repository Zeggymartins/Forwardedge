<?php

namespace App\Mail;

use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\SerializesModels;

class EmailCampaignMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmailCampaign $campaign,
        public ?string $recipientName = null,
        public ?string $recipientEmail = null
    ) {
        $this->subject($campaign->subject);
    }

    public function middleware(): array
    {
        return [new RateLimited('mail')];
    }

    public function build(): self
    {
        $ctaLink = $this->resolveCtaLink();

        return $this->view('emails.campaign')
            ->with([
                'campaign' => $this->campaign,
                'recipientName' => $this->recipientName,
                'recipientEmail' => $this->recipientEmail,
                'ctaLink' => $ctaLink,
            ]);
    }

    protected function resolveCtaLink(): ?string
    {
        if (!$this->campaign->cta_link) {
            return null;
        }

        if (!$this->recipientEmail || !$this->campaign->cta_email_param) {
            return $this->campaign->cta_link;
        }

        $separator = str_contains($this->campaign->cta_link, '?') ? '&' : '?';

        return $this->campaign->cta_link . $separator . $this->campaign->cta_email_param . '=' . urlencode($this->recipientEmail);
    }
}

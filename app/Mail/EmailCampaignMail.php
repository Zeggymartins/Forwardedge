<?php

namespace App\Mail;

use App\Models\EmailCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailCampaignMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public EmailCampaign $campaign,
        public ?string $recipientName = null
    ) {
        $this->subject($campaign->subject);
    }

    public function build(): self
    {
        return $this->view('emails.campaign')
            ->with([
                'campaign' => $this->campaign,
                'recipientName' => $this->recipientName,
            ]);
    }
}

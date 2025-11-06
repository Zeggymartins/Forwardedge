<?php

namespace App\Jobs\Mailchimp;

use App\Services\MailchimpTransactional;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendEvent implements ShouldQueue
{
    use Queueable, Batchable;

    public $tries = 3;

    public function __construct(
        protected array $message,
        protected ?string $template = null,
        protected array $templateContent = []
    ) {
    }

    public function handle(): void
    {
        if (blank(config('services.mailchimp.transactional_key'))) {
            Log::warning('Mailchimp transactional key missing, skipping send.');
            return;
        }

        if ($this->template) {
            MailchimpTransactional::sendTemplate($this->template, $this->message, $this->templateContent);
            return;
        }

        MailchimpTransactional::sendMessage($this->message);
    }
}

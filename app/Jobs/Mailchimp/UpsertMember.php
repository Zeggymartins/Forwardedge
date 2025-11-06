<?php

namespace App\Jobs\Mailchimp;

use App\Jobs\Mailchimp\UpdateTags;
use App\Services\Mailchimp;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use MailchimpMarketing\ApiException;

class UpsertMember implements ShouldQueue
{
    use Queueable, Batchable;

    public $tries = 3;

    public function __construct(
        protected string $email,
        protected array $mergeFields = [],
        protected array $options = []
    ) {
        $this->email = strtolower(trim($this->email));
    }

    public function handle(): void
    {
        $listId    = $this->options['list_id'] ?? Mailchimp::listId();
        $status    = ($this->options['double_opt_in'] ?? config('services.mailchimp.double_opt_in')) ? 'pending' : 'subscribed';
        $tags      = Arr::wrap($this->options['tags'] ?? []);
        $client    = Mailchimp::client();
        $hash      = md5($this->email);

        try {
            $client->lists->setListMember($listId, $hash, [
                'email_address' => $this->email,
                'status'        => $status,
                'status_if_new' => $status,
                'merge_fields'  => $this->mergeFields,
            ]);

            if (!empty($tags)) {
                UpdateTags::dispatch($this->email, $tags, $listId)->onQueue($this->options['queue'] ?? null);
            }
        } catch (ApiException $e) {
            Log::error('Mailchimp member sync failed (api)', [
                'email'   => $this->email,
                'message' => $e->getMessage(),
                'body'    => $e->getResponseBody(),
            ]);

            throw $e;
        } catch (\Throwable $e) {
            Log::error('Mailchimp member sync failed (client)', [
                'email'     => $this->email,
                'exception' => get_class($e),
                'message'   => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

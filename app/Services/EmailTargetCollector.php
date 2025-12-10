<?php

namespace App\Services;

use App\Models\EmailContact;
use App\Models\EventRegistration;
use App\Models\Message;
use App\Models\ScholarshipApplication;
use App\Models\User;
use App\Services\Mailchimp;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class EmailTargetCollector
{
    protected array $sourceMap = [
        'contacts' => 'Manual contacts',
        'users' => 'Registered users',
        'messages' => 'Contact form leads',
        'events' => 'Event registrations',
        'scholarships' => 'Scholarship applicants',
        'mailchimp' => 'API - Forward Edge key (Mailchimp)',
    ];

    public function availableSources(): array
    {
        return $this->sourceMap;
    }

    public function all(array $options = []): Collection
    {
        $allowedSources = collect($options['sources'] ?? [])->filter()->map(fn($source) => strtolower($source))->values()->all();
        $shouldInclude = function (string $source) use ($allowedSources): bool {
            if (empty($allowedSources)) {
                return true;
            }
            return in_array(strtolower($source), $allowedSources, true);
        };

        $targets = collect();

        if ($shouldInclude('contacts')) {
            $targets = $targets->merge(
                EmailContact::query()
                    ->select('email', 'name', 'source')
                    ->get()
                    ->map(fn ($contact) => [
                        'email' => $contact->email,
                        'name'  => $contact->name,
                        'source' => $contact->source ?? 'contacts',
                    ])
            );
        }

        if ($shouldInclude('users')) {
            $targets = $targets->merge(
                User::query()
                    ->select('email', 'name')
                    ->get()
                    ->map(fn ($user) => [
                        'email' => $user->email,
                        'name'  => $user->name,
                        'source' => 'users',
                    ])
            );
        }

        if ($shouldInclude('messages')) {
            $targets = $targets->merge(
                Message::query()
                    ->select('email', 'name')
                    ->get()
                    ->map(fn ($message) => [
                        'email' => $message->email,
                        'name'  => $message->name,
                        'source' => 'messages',
                    ])
            );
        }

        if ($shouldInclude('events')) {
            $targets = $targets->merge(
                EventRegistration::query()
                    ->select('email', 'first_name', 'last_name')
                    ->get()
                    ->map(fn ($registration) => [
                        'email' => $registration->email,
                        'name'  => trim(implode(' ', array_filter([$registration->first_name, $registration->last_name]))),
                        'source' => 'events',
                    ])
            );
        }

        if ($shouldInclude('scholarships')) {
            $targets = $targets->merge(
                ScholarshipApplication::query()
                    ->select('form_data')
                    ->get()
                    ->map(function ($application) {
                        $email = Arr::get($application->form_data, 'email')
                            ?? Arr::get($application->form_data, 'contact.email');

                        $name = Arr::get($application->form_data, 'name')
                            ?? Arr::get($application->form_data, 'full_name');

                        return [
                            'email' => $email,
                            'name'  => $name,
                            'source' => 'scholarships',
                        ];
                    })
            );
        }

        if ($shouldInclude('mailchimp')) {
            $targets = $targets->merge($this->fetchMailchimpContacts());
        }

        $targets = $this->sanitize($targets);

        $include = collect($options['include'] ?? [])->filter(fn ($value) => is_string($value))->map(fn ($email) => strtolower(trim($email)))->filter()->values();
        if ($include->isNotEmpty()) {
            $targets = $targets->merge(
                $include->map(fn ($email) => [
                    'email' => $email,
                    'name' => null,
                    'source' => 'manual_include',
                ])
            );
            $targets = $this->sanitize($targets);
        }

        $exclude = collect($options['exclude'] ?? [])->filter(fn ($value) => is_string($value))->map(fn ($email) => strtolower(trim($email)))->filter()->values();
        if ($exclude->isNotEmpty()) {
            $targets = $targets->reject(function ($row) use ($exclude) {
                return in_array(strtolower($row['email']), $exclude->all(), true);
            })->values();
        }

        return $targets;
    }

    protected function fetchMailchimpContacts(): Collection
    {
        $key = config('services.mailchimp.key');
        $server = config('services.mailchimp.server_prefix');

        if (blank($key) || blank($server)) {
            return collect();
        }

        try {
            $listId = Mailchimp::listId();
            $listsApi = Mailchimp::listsApi();
        } catch (\Throwable $e) {
            Log::warning('Mailchimp contacts skipped: configuration issue.', [
                'message' => $e->getMessage(),
            ]);
            return collect();
        }

        $contacts = collect();
        $offset = 0;
        $count = 500;
        $total = null;

        try {
            do {
                $response = $listsApi->getListMembersInfo($listId, [
                    'count' => $count,
                    'offset' => $offset,
                    'fields' => 'members.email_address,members.merge_fields,members.status,total_items',
                ]);

                $responseData = (array) $response;
                $members = collect($responseData['members'] ?? []);

                if ($members->isEmpty()) {
                    break;
                }

                $contacts = $contacts->merge(
                    $members
                        ->filter(function ($member) {
                            $data = is_array($member) ? $member : (array) $member;
                            $status = strtolower($data['status'] ?? '');
                            return in_array($status, ['subscribed', 'pending'], true);
                        })
                        ->map(function ($member) {
                            $data = is_array($member) ? $member : (array) $member;
                            $merge = [];
                            if (isset($data['merge_fields'])) {
                                $merge = is_array($data['merge_fields']) ? $data['merge_fields'] : (array) $data['merge_fields'];
                            }

                            $first = $merge['FNAME'] ?? null;
                            $last = $merge['LNAME'] ?? null;

                            return [
                                'email' => $data['email_address'] ?? null,
                                'name'  => trim(implode(' ', array_filter([$first, $last]))) ?: null,
                                'source' => 'mailchimp',
                            ];
                        })
                );

                $offset += $count;
                $total = $responseData['total_items'] ?? null;

                if (is_null($total) && $members->count() < $count) {
                    break;
                }
            } while (is_null($total) ? true : $offset < $total);
        } catch (\Throwable $e) {
            Log::error('Failed to fetch Mailchimp contacts.', [
                'message' => $e->getMessage(),
            ]);
        }

        return $contacts;
    }

    protected function sanitize(Collection $targets): Collection
    {
        return $targets
            ->filter(fn ($row) => !empty($row['email']))
            ->map(function ($row) {
                $row['email'] = strtolower(trim($row['email']));
                $row['name'] = $row['name'] ?? null;
                return $row;
            })
            ->filter(fn ($row) => filter_var($row['email'], FILTER_VALIDATE_EMAIL))
            ->unique('email')
            ->values();
    }
}

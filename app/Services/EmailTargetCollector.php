<?php

namespace App\Services;

use App\Models\EmailContact;
use App\Models\EventRegistration;
use App\Models\Message;
use App\Models\ScholarshipApplication;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class EmailTargetCollector
{
    public function all(): Collection
    {
        $targets = collect();

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

        return $this->sanitize($targets);
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

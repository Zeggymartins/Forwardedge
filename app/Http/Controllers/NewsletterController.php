<?php

namespace App\Http\Controllers;

use App\Jobs\Mailchimp\UpsertMember;
use App\Mail\NewsletterWelcomeMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        if (
            $request->expectsJson() ||
            $request->input('source') === 'builder_form' ||
            $request->has('fields') ||
            $request->has('newsletter_form')
        ) {
            return $this->handleDynamicForm($request);
        }

        $data = $request->validate([
            'email'        => 'required|email:rfc,dns',
            'first_name'   => 'nullable|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'double_optin' => 'nullable|boolean',
            'tags'         => 'nullable|array|max:10',
            'tags.*'       => 'nullable|string|max:50',
        ]);

        $email = strtolower($data['email']);
        $merge = [
            'FNAME' => $data['first_name'] ?? '',
            'LNAME' => $data['last_name'] ?? '',
        ];

        $merge['FNAME'] = $merge['FNAME'] !== '' ? $merge['FNAME'] : 'Subscriber';
        $merge['LNAME'] = $merge['LNAME'] !== '' ? $merge['LNAME'] : '-';

        UpsertMember::dispatchSync($email, $merge, [
            'double_opt_in' => $data['double_optin'] ?? config('services.mailchimp.double_opt_in'),
            'tags'          => $data['tags'] ?? ['Website'],
        ]);

        Mail::to($email)->send(new NewsletterWelcomeMail($merge['FNAME'] ?: 'Subscriber'));

        $message = ($data['double_optin'] ?? config('services.mailchimp.double_opt_in'))
            ? 'Almost done! Please confirm the email we just sent you.'
            : 'Thanks! You are subscribed 🎉';

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    protected function handleDynamicForm(Request $request)
    {
        if (!$request->has('tags') && $request->filled('form_tags')) {
            $request->merge([
                'tags' => collect(explode(',', (string) $request->input('form_tags')))
                    ->map(fn ($tag) => trim($tag))
                    ->filter()
                    ->values()
                    ->all(),
            ]);
        }

        if (!$request->has('fields') && $request->has('newsletter_form')) {
            $forms = $request->input('newsletter_form');
            $blockId = $request->input('block_id');

            if (!$blockId && is_array($forms)) {
                $blockId = array_key_first($forms);
            }

            $request->merge([
                'block_id' => $blockId,
                'fields'   => $this->mapLegacyFieldsToPayload($request, $blockId),
            ]);
        }

        $payload = $request->validate([
            'block_id'       => 'nullable|integer',
            'fields'         => 'required|array|min:1',
            'fields.*.label' => 'nullable|string|max:120',
            'fields.*.name'  => 'required|string|max:60',
            'fields.*.type'  => 'required|string|in:text,email,tel,textarea',
            'fields.*.value' => 'nullable|string|max:5000',
            'fields.*.required' => 'nullable|boolean',
            'tags'           => 'nullable|array|max:10',
            'tags.*'         => 'nullable|string|max:50',
        ]);

        $fields = collect($payload['fields']);
        $emailField = $fields->first(fn ($field) => $field['type'] === 'email' && filled($field['value'] ?? null));

        if (!$emailField) {
            throw ValidationException::withMessages([
                'fields' => 'Please include an email field in your form.',
            ]);
        }

        $emailValue = trim((string) $emailField['value']);

        if (!filter_var($emailValue, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages([
                'fields' => 'Enter a valid email address.',
            ]);
        }

        $normalizedMerge = $this->buildMergeFields($fields);

        UpsertMember::dispatchSync(
            strtolower($emailValue),
            $normalizedMerge,
            [
                'tags' => !empty($payload['tags']) ? $payload['tags'] : ['Newsletter'],
            ]
        );

        Mail::to($emailValue)->send(new NewsletterWelcomeMail($normalizedMerge['FNAME'] ?? 'Subscriber'));

        $message = 'Thanks for joining our newsletter! 🎉';

        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'ok',
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    protected function mapLegacyFieldsToPayload(Request $request, $blockId): array
    {
        if (!$blockId) {
            return [];
        }

        $values = Arr::get($request->input('newsletter_form'), $blockId, []);
        $meta   = Arr::get($request->input('field_meta'), $blockId, []);

        $fields = [];

        foreach ($meta as $name => $info) {
            $fields[] = [
                'label'    => $info['label'] ?? Str::title(str_replace('_', ' ', $name)),
                'name'     => $name,
                'type'     => $info['type'] ?? 'text',
                'required' => filter_var($info['required'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'value'    => $values[$name] ?? '',
            ];
        }

        if (empty($fields) && is_array($values)) {
            foreach ($values as $name => $value) {
                $fields[] = [
                    'label' => Str::title(str_replace('_', ' ', $name)),
                    'name'  => $name,
                    'type'  => 'text',
                    'required' => false,
                    'value' => $value,
                ];
            }
        }

        return $fields;
    }

    protected function buildMergeFields($fields): array
    {
        $fields = collect($fields);

        $first = $fields->first(function ($field) {
            return Str::contains(strtolower($field['name'] ?? ''), ['first', 'fname']);
        });

        $last = $fields->first(function ($field) {
            return Str::contains(strtolower($field['name'] ?? ''), ['last', 'lname']);
        });

        $phone = $fields->first(fn ($field) => $field['type'] === 'tel');

        $merge = [
            'FNAME' => trim((string) ($first['value'] ?? '')),
            'LNAME' => trim((string) ($last['value'] ?? '')),
            'PHONE' => trim((string) ($phone['value'] ?? '')),
        ];

        if ($merge['FNAME'] === '') {
            $full = $fields->first(function ($field) {
                $name = strtolower($field['name'] ?? '');
                $label = strtolower($field['label'] ?? '');
                return Str::contains($name, ['full_name', 'fullname', 'full-name', 'name'])
                    || Str::contains($label, ['full name', 'fullname']);
            });

            if ($full && filled($full['value'] ?? null)) {
                $parts = preg_split('/\s+/', trim((string) $full['value']));
                $merge['FNAME'] = $parts[0] ?? '';
                $merge['LNAME'] = $merge['LNAME'] ?: (implode(' ', array_slice($parts, 1)) ?: '');
            }
        }

        $fields->each(function ($field, $index) use (&$merge) {
            $key = strtoupper(Str::limit(preg_replace('/[^A-Za-z0-9_]/', '', Str::snake($field['name'] ?? 'field')), 10, ''));
            if ($key === '' || array_key_exists($key, $merge)) {
                $key = 'FIELD' . ($index + 1);
            }
            $merge[$key] = trim((string) ($field['value'] ?? ''));
        });

        $merge['FNAME'] = $merge['FNAME'] !== '' ? $merge['FNAME'] : 'Subscriber';
        $merge['LNAME'] = $merge['LNAME'] !== '' ? $merge['LNAME'] : '-';

        return $merge;
    }
}

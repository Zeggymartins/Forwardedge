<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class TrustedEmailDomain implements ValidationRule
{
    protected array $blockedDomains;
    protected array $blockedTlds;

    public function __construct(?array $blockedDomains = null, ?array $blockedTlds = null)
    {
        $this->blockedDomains = $this->normalizeList($blockedDomains ?? config('mail.blocked_domains', []));
        $this->blockedTlds    = $this->normalizeList($blockedTlds ?? config('mail.blocked_tlds', []));
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = strtolower(trim((string) $value));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fail('Enter a valid email address.');
            return;
        }

        $domain = Str::after($email, '@');
        if (blank($domain)) {
            $fail('Enter a valid email address.');
            return;
        }

        if (in_array($domain, $this->blockedDomains, true)) {
            $fail('Use a trusted email domain.');
            return;
        }

        $tld = strtolower(Str::afterLast($domain, '.'));
        if ($tld && in_array($tld, $this->blockedTlds, true)) {
            $fail('Use a trusted email domain (no disposable addresses).');
        }
    }

    protected function normalizeList(array $items): array
    {
        return array_values(array_unique(array_filter(array_map(function ($item) {
            return strtolower(trim($item));
        }, $items))));
    }
}

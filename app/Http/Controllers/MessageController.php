<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Service;
use App\Rules\TrustedEmailDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    public function create()
    {
        $services = Service::all();
        return view('user.pages.contact', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cfName2'    => 'required|string|max:255',
            'cfEmail2'   => ['required', 'email:rfc,dns', new TrustedEmailDomain()],
            'cfPhone2'   => 'nullable|string|max:20',
            'cfSubject2' => 'required|exists:services,id',
            'cfMessage2' => 'required|string',
            'hp_field'   => ['nullable', 'prohibited'],
            'g-recaptcha-response' => ['required', new \App\Rules\Recaptcha('contact')],
        ]);

        // Detect and block spam patterns
        $this->detectSpam($validated['cfName2'], $validated['cfEmail2'], $validated['cfMessage2'], $request->ip());

        // Save to DB
        $message = Message::create([
            'name'       => $validated['cfName2'],
            'email'      => $validated['cfEmail2'],
            'phone'      => $validated['cfPhone2'],
            'service_id' => $validated['cfSubject2'],
            'message'    => $validated['cfMessage2'],
        ]);

        $recipient = config('mail.contact_recipient', config('mail.from.address'));

        Mail::send('emails.contact', ['messageData' => $message], function ($mail) use ($message, $recipient) {
            $mail->to($recipient)
                ->replyTo($message->email, $message->name)
                ->subject('New Contact Message from ' . $message->name);
        });

        return back()->with('success', 'Message sent successfully!');
    }

    /**
     * Detect and block spam patterns
     */
    protected function detectSpam(string $name, string $email, string $message, string $ip): void
    {
        // 1. Check specific spam name patterns
        $spamNamePatterns = config('spam.name_patterns', [
            'roberthen',
            'robert hen',
            'robert-hen',
            'robert_hen',
        ]);

        $nameLower = strtolower(trim($name));
        $nameLowerNoSpaces = preg_replace('/\s+/', '', $nameLower);

        foreach ($spamNamePatterns as $pattern) {
            $patternNoSpaces = preg_replace('/\s+/', '', strtolower($pattern));
            if (str_contains($nameLowerNoSpaces, $patternNoSpaces)) {
                $this->logAndBlock('Name pattern match', $name, $email, $ip, $pattern);
            }
        }

        // 2. Check for suspicious patterns in message content
        $messageLower = strtolower($message);
        $spamKeywords = config('spam.message_keywords', [
            'click here',
            'buy now',
            'limited time',
            'act now',
            'viagra',
            'cialis',
            'weight loss',
            'make money',
            'work from home',
            'earn cash',
            'casino',
            'lottery',
            'winner',
            'congratulations you won',
            'bitcoin',
            'crypto investment',
            'double your money',
            'guaranteed income',
            'млн',
            'prize',
        ]);

        $matchCount = 0;
        foreach ($spamKeywords as $keyword) {
            if (str_contains($messageLower, strtolower($keyword))) {
                $matchCount++;
            }
        }

        // If 2 or more spam keywords found, likely spam
        if ($matchCount >= 2) {
            $this->logAndBlock('Multiple spam keywords detected', $name, $email, $ip, "Keywords: $matchCount matches");
        }

        // 3. Check for excessive URLs in message
        preg_match_all('/(https?:\/\/|www\.)/i', $message, $urlMatches);
        if (count($urlMatches[0]) > 3) {
            $this->logAndBlock('Excessive URLs in message', $name, $email, $ip, 'URL count: ' . count($urlMatches[0]));
        }

        // 4. Check message length - spam is often very short or very long
        $messageLength = strlen($message);
        if ($messageLength < 10) {
            $this->logAndBlock('Message too short', $name, $email, $ip, "Length: $messageLength chars");
        }
        if ($messageLength > 5000) {
            $this->logAndBlock('Message too long', $name, $email, $ip, "Length: $messageLength chars");
        }

        // 5. Check for excessive capitalization (>50% caps)
        $upperCount = strlen(preg_replace('/[^A-Z]/', '', $message));
        $letterCount = strlen(preg_replace('/[^A-Za-z]/', '', $message));
        if ($letterCount > 0 && ($upperCount / $letterCount) > 0.5) {
            $this->logAndBlock('Excessive capitalization', $name, $email, $ip, sprintf('%.0f%% caps', ($upperCount / $letterCount) * 100));
        }

        // 6. Check for repeated characters (e.g., "HELLOOOOO", "!!!!!")
        if (preg_match('/(.)\1{4,}/', $message)) {
            $this->logAndBlock('Repeated characters detected', $name, $email, $ip, 'Pattern: same char 5+ times');
        }

        // 7. Check for non-English spam (Cyrillic, Chinese characters when not expected)
        if (preg_match('/[\x{0400}-\x{04FF}]/u', $name . $email . $message)) {
            $this->logAndBlock('Cyrillic characters detected', $name, $email, $ip, 'Non-English spam');
        }

        // 8. Check blacklisted emails
        $blacklistedEmails = config('spam.blacklisted_emails', []);
        if (in_array(strtolower($email), array_map('strtolower', $blacklistedEmails))) {
            $this->logAndBlock('Blacklisted email', $name, $email, $ip, null);
        }

        // 9. Check blacklisted IPs
        $blacklistedIps = config('spam.blacklisted_ips', []);
        if (in_array($ip, $blacklistedIps)) {
            $this->logAndBlock('Blacklisted IP', $name, $email, $ip, null);
        }

        // 10. Check for suspicious email patterns
        if (preg_match('/[0-9]{5,}@/', $email)) {
            $this->logAndBlock('Suspicious email pattern', $name, $email, $ip, 'Email has 5+ consecutive numbers');
        }

        // 11. Check for disposable/temporary email domains
        $disposableDomains = config('spam.disposable_domains', [
            'tempmail.com',
            'guerrillamail.com',
            '10minutemail.com',
            'throwaway.email',
            'mailinator.com',
            'trashmail.com',
            'temp-mail.org',
        ]);

        $emailDomain = strtolower(substr(strrchr($email, '@'), 1));
        if (in_array($emailDomain, $disposableDomains)) {
            $this->logAndBlock('Disposable email domain', $name, $email, $ip, "Domain: $emailDomain");
        }
    }

    /**
     * Log spam detection and block the submission
     */
    protected function logAndBlock(string $reason, string $name, string $email, string $ip, ?string $details): void
    {
        \Log::warning('Spam detected', [
            'reason' => $reason,
            'name' => $name,
            'email' => $email,
            'ip' => $ip,
            'details' => $details,
            'timestamp' => now()->toDateTimeString(),
        ]);

        abort(422, 'Your submission could not be processed. Please contact us directly if you need assistance.');
    }
}

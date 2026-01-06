<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Service;
use App\Rules\TrustedEmailDomain;
use Illuminate\Http\Request;
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
        // Spam name patterns (case-insensitive)
        $spamNamePatterns = [
            'roberthen',
            'robert hen',
            'robert-hen',
            'robert_hen',
        ];

        $nameLower = strtolower(trim($name));
        $nameLower = preg_replace('/\s+/', '', $nameLower); // Remove all spaces

        foreach ($spamNamePatterns as $pattern) {
            $patternNoSpaces = preg_replace('/\s+/', '', $pattern);
            if (str_contains($nameLower, $patternNoSpaces)) {
                \Log::warning('Spam detected - Name pattern match', [
                    'name' => $name,
                    'email' => $email,
                    'ip' => $ip,
                    'pattern' => $pattern,
                ]);

                abort(422, 'Your submission could not be processed. Please contact us directly if you need assistance.');
            }
        }

        // Check blacklisted emails
        $blacklistedEmails = config('spam.blacklisted_emails', []);
        if (in_array(strtolower($email), array_map('strtolower', $blacklistedEmails))) {
            \Log::warning('Spam detected - Blacklisted email', [
                'name' => $name,
                'email' => $email,
                'ip' => $ip,
            ]);

            abort(422, 'Your submission could not be processed. Please contact us directly if you need assistance.');
        }

        // Check blacklisted IPs
        $blacklistedIps = config('spam.blacklisted_ips', []);
        if (in_array($ip, $blacklistedIps)) {
            \Log::warning('Spam detected - Blacklisted IP', [
                'name' => $name,
                'email' => $email,
                'ip' => $ip,
            ]);

            abort(422, 'Your submission could not be processed. Please contact us directly if you need assistance.');
        }
    }
}

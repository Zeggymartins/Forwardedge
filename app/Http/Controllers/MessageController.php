<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Service;
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
            'cfEmail2'   => 'required|email',
            'cfPhone2'   => 'nullable|string|max:20',
            'cfSubject2' => 'required|exists:services,id',
            'cfMessage2' => 'required|string',
        ]);

        // Save to DB
        $message = Message::create([
            'name'       => $validated['cfName2'],
            'email'      => $validated['cfEmail2'],
            'phone'      => $validated['cfPhone2'],
            'service_id' => $validated['cfSubject2'],
            'message'    => $validated['cfMessage2'],
        ]);

        // Send Email
        Mail::send('emails.contact', ['messageData' => $message], function ($mail) use ($message) {
            $mail->to('your@email.com') 
                ->subject('New Contact Message from ' . $message->name);
        });

        return back()->with('success', 'Message sent successfully!');
    }
}

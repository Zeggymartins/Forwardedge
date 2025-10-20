<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReplyToMessageMail;
use App\Models\Message;
use App\Models\MessageReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminMessageController extends Controller
{
    public function index(Request $r)
    {
        $q = trim((string) $r->input('q'));
        $filter = $r->input('filter', 'all');
        $sort = $r->input('sort', 'new');

        $messages = Message::with('service')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('message', 'like', "%{$q}%");
                });
            })
            ->when($filter === 'unread', fn($qq) => $qq->whereNull('read_at'))
            ->when($filter === 'read', fn($qq) => $qq->whereNotNull('read_at'))
            ->when($sort === 'old', fn($qq) => $qq->oldest(), fn($qq) => $qq->latest())
            ->paginate(20);

        $stats = [
            'all' => Message::count(),
            'unread' => Message::unread()->count(),
            'read' => Message::read()->count(),
        ];

        return view('admin.pages.messages.index', compact('messages', 'stats', 'q', 'filter', 'sort'));
    }

    // JSON payload for modal (marks as read on open)
    public function showJson(Message $message)
    {
        if (is_null($message->read_at)) {
            $message->update(['read_at' => now()]);
        }
        $message->load(['service:id,title', 'replies.admin:id,name']);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'name' => $message->name,
                'email' => $message->email,
                'phone' => $message->phone,
                'subject' => $message->subject ?? ('Message #' . $message->id),
                'service' => $message->service?->title,
                'body' => $message->message,
                'read_at' => optional($message->read_at)->toDateTimeString(),
                'created_at' => optional($message->created_at)->toDateTimeString(),
            ],
            'replies' => $message->replies->map(fn($r) => ([
                'id' => $r->id,
                'subject' => $r->subject,
                'body' => $r->body,
                'to_email' => $r->to_email,
                'admin' => $r->admin?->name,
                'created_at' => optional($r->created_at)->toDateTimeString(),
                'mailed_at' => optional($r->mailed_at)->toDateTimeString(),
            ]))
        ]);
    }
}
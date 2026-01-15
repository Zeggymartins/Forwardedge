<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\Orders;
use App\Models\EmailAccessToken;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPaid extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $zipPath;

    public function __construct(Orders $order, $zipPath)
    {
        $this->order = $order;
        $this->zipPath = $zipPath;
    }

    public function build()
    {
        $accessLinks = [];
        foreach ($this->order->orderItems as $item) {
            if (!$item->course) {
                continue;
            }

            $token = EmailAccessToken::issueForCourse(
                $this->order->user,
                $item->course,
                now()->addHours(72)
            );
            $accessLinks[$item->id] = route('email.access.token', ['token' => $token]);
        }

        $mail = $this->subject("Order Confirmation - Your Course Bundle")
            ->view('emails.orders.paid')
            ->with([
                'order' => $this->order,
                'accessLinks' => $accessLinks,
            ]);

        if ($this->zipPath && file_exists($this->zipPath)) {
            $mail->attach($this->zipPath, [
                'as' => 'course_contents.zip',
                'mime' => 'application/zip'
            ]);
        }

        return $mail;
    }
}

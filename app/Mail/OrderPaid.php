<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\Orders;
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
        $mail = $this->subject("Order Confirmation - Your Course Bundle")
            ->view('emails.orders.paid')
            ->with(['order' => $this->order]);

        if ($this->zipPath && file_exists($this->zipPath)) {
            $mail->attach($this->zipPath, [
                'as' => 'course_contents.zip',
                'mime' => 'application/zip'
            ]);
        }

        return $mail;
    }
}

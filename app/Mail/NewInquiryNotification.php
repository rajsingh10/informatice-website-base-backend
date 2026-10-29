<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewInquiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        $sender = $this->inquiry->name ?: 'Website Visitor';
        $service = $this->inquiry->serviceInterested ? " [{$this->inquiry->serviceInterested}]" : '';

        return new Envelope(
            subject: "New Contact Inquiry from {$sender}{$service}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-inquiry',
        );
    }
}


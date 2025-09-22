<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PublishArticle extends Mailable
{
    use Queueable, SerializesModels;
    public $fullname;
    public $title;
    public $stage;

    /**
     * Create a new message instance.
     */
    public function __construct($fullname, $title)
    {
        $this->fullname = $fullname;
        $this->title = $title;
        $this->stage = 10;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Published Article: ' . $this->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.accepted-mail-stage5',
        );
        // return new Content(
        //     view: 'mail.publish-mail',
        // );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

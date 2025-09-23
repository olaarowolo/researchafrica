<?php

namespace App\Modules\AfriScribe\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;

class AfriscribeRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $senderName;
    public $senderEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $senderName = null, $senderEmail = null)
    {
        $this->data = $data;
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->data['service_type'] === 'proofreading'
            ? 'New AfriScribe Proofreading Request'
            : 'New AfriScribe Request';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.afriscribe_request',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        // Attach file if it exists
        if (!empty($this->data['file_path']) && !empty($this->data['original_filename'])) {
            $attachments[] = Attachment::fromStorage($this->data['file_path'])
                ->as($this->data['original_filename']);
        }

        return $attachments;
    }

    /**
     * Set custom sender information
     */
    public function from($address, $name = null)
    {
        $this->senderEmail = $address;
        $this->senderName = $name;
        return $this;
    }
}

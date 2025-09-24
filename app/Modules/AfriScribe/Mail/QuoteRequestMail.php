<?php

namespace App\Modules\AfriScribe\Mail;

use App\Modules\AfriScribe\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class QuoteRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public QuoteRequest $quoteRequest;

    /**
     * Create a new message instance.
     */
    public function __construct(QuoteRequest $quoteRequest)
    {
        $this->quoteRequest = $quoteRequest;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Quote Request - AfriScribe Proofreading Service',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.quote_request',
            with: [
                'quoteRequest' => $this->quoteRequest,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->quoteRequest->file_path && \Storage::disk('public')->exists($this->quoteRequest->file_path)) {
            $attachments[] = Attachment::fromStorageDisk('public', $this->quoteRequest->file_path)
                ->as($this->quoteRequest->original_filename)
                ->withMime('application/octet-stream');
        }

        return $attachments;
    }
}

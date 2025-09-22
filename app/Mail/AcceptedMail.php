<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class AcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fullname;
    public $stage;
    public $status;
    public $title;

    /**
     * Create a new message instance.
     */
    public function __construct(string $fullname, int $stage, string $title = "")
    {
        $this->fullname = $fullname;
        $this->status = $stage;
        switch ($stage) {
            case 2:
                $this->stage = 1;
                break;
            case 4:
                $this->stage = 2;
                break;
            case 6:
                $this->stage = 3;
                break;
            case 12:
                $this->stage = 4;
                break;
            case 10:
                $this->stage = 5;
                break;
            default:
                $this->stage = $stage;
                break;
        }
        $this->title = $title;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Accepted Article - Stage ' . $this->stage
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
      if ($this->status == 2) {
        return new Content(
            view: 'mail.accepted-mail-stage1'
        );
      }
      if ($this->status == 4) {
        return new Content(
            view: 'mail.accepted-mail-stage2'
        );
      }
      if ($this->status == 6) {
        return new Content(
            view: 'mail.accepted-mail-stage3'
        );
      }
      if ($this->status == 12) {
        return new Content(
            view: 'mail.accepted-mail-stage4'
        );
      }
      if ($this->status == 10) {
        return new Content(
            view: 'mail.accepted-mail-stage5'
        );
      }
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
<?php

namespace App\Modules\AfriScribe\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class AfriscribeRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject('New AfriScribe Proofread Request')
                    ->view('emails.afriscribe_request');

        // Attach file if it exists
        if (!empty($this->data['file_path']) && !empty($this->data['original_filename'])) {
            $mail->attachFromStorage($this->data['file_path'], $this->data['original_filename']);
        }

        return $mail;
    }
}

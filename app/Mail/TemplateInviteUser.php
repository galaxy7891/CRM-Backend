<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateInviteUser extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $url;
    public $nama;
    public $invited_by;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $url, $nama, $invited_by)
    {
        $this->email = $email;
        $this->url = $url;
        $this->nama = $nama;
        $this->invited_by = $invited_by;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'LoyalCust: Invitation Account',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'invitation',
            with: [
                'email' => $this->email,
                'url' => $this->url,
                'nama' => $this->nama,
                'invited_by' => $this->invited_by
            ],
        );
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

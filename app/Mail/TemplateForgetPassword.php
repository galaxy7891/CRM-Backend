<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateForgetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $url;
    public $nama;

    /**
     * Create a new message instance.
     */
    public function __construct($email, $url, $nama)
    {
        $this->email = $email;
        $this->url = $url;
        $this->nama = $nama;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'LoyalCust: Reset Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'forgetpassword',
            with: [
                'email' => $this->email,
                'url' => $this->url,
                'nama' => $this->nama
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

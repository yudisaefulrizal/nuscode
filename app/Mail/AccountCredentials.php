<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $password,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Akun Anda di NUSCODE',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.account-credentials',
            with: [
                'user' => $this->user,
                'password' => $this->password,
                'whatsappUrl' => 'https://wa.me/'.config('midtrans.whatsapp_admin_number')
                    .'?text='.rawurlencode('Halo, saya butuh bantuan dengan akun NUSCODE saya'),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

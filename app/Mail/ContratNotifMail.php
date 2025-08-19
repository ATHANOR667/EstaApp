<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContratNotifMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contrat;
    public $subject;
    public $notificationMessage; // Renamed from $message

    /**
     * Crée une nouvelle instance de message.
     */
    public function __construct($contrat, $subject, $notificationMessage)
    {
        $this->contrat = $contrat;
        $this->subject = $subject;
        $this->notificationMessage = $notificationMessage; // Renamed
    }

    /**
     * Obtient l'enveloppe du message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Obtient le contenu du message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.contrat-notif-mail',
            with: [
                'contrat' => $this->contrat,
                'notificationMessage' => $this->notificationMessage,
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

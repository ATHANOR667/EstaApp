<?php

namespace App\Mail;
use App\Models\Contrat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contrat $contrat;
    public string $signedUrl;

    public function __construct(Contrat $contrat, string $signedUrl)
    {
        $this->contrat = $contrat;
        $this->signedUrl = $signedUrl; // Assigner l'URL
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Signature de votre contrat',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contract-mail',
        );
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContractNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $contrat;
    public $signedUrl;
    public $customMessage;

    public function __construct($contrat, $signedUrl, $customMessage = '')
    {
        $this->contrat = $contrat;
        $this->signedUrl = $signedUrl;
        $this->customMessage = $customMessage;
    }

    public function build()
    {
        return $this->subject('Nouveau contrat à examiner')
            ->view('emails.contract-notification-html')
            ->with([
                'artiste' => $this->contrat->prestation->artiste->nom,
                'date' => \Carbon\Carbon::parse($this->contrat->prestation->date_prestation)->locale('fr')->isoFormat('D MMMM YYYY'),
                'lieu' => $this->contrat->prestation->lieu_prestation,
                'url' => $this->signedUrl,
                'expiresAt' => now()->addHours(48)->toDateTimeString(),
                'customMessage' => $this->customMessage,
            ]);
    }
}

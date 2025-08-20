<?php

namespace App\Models;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Contrat extends Model
{
    use HasFactory, SoftDeletes , Notifiable;

    const STATUSES = [
        'draft' => 'Brouillon',
        'pending' => 'En cours d\'envoi',
        'sent' => 'En attente de l’organisateur',
        'delivered' => 'Lu et en attente de réponse',
        'completed' => 'Signé',
        'declined' => 'Rejeté',
        'voided' => 'voided',
    ];

    protected $fillable = [
        'prestation_id',
        'docusign_envelope_id',
        'docusign_document_id',
        'content',
        'signature_artiste_representant',
        'signature_contractant',
        'motif',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function prestation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Prestation::class);
    }

    public function versements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Versement::class);
    }

    public function routeNotificationForMail($notification)
    {
        return $this->prestation->contact_artiste;
    }

    public function routeNotificationForWhatsApp($notification)
    {
        return $this->prestation->contact_artiste;
    }

    public function routeNotificationForSms($notification)
    {
        return $this->prestation->contact_artiste;
    }

    public function download(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $qrCodeUrl = URL::temporarySignedRoute(
            name : 'contrats.download_pdf',
            expiration:  now()->addDays(7),
            parameters : ['contrat' => $this->id] ,
            absolute: true,
        );
        $qrCodeSvg = QrCode::size(150)->generate($qrCodeUrl)->toHtml();
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.view_contract', [
            'contrat' => $this,
            'qrCodeSvg' => $qrCodeBase64,
            'dateEmission' => now()->format('d/m/Y'),
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'contrat_' . $this->id . '.pdf');
    }
}

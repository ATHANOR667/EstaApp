<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contrat extends Model
{
    use HasFactory, SoftDeletes;

    const STATUSES = [
        'draft' => 'Brouillon',
        'signed_by_artist' => 'Signé par l’artiste',
        'pending_organizer' => 'En attente de l’organisateur',
        'signed' => 'Signé',
        'rejected' => 'Rejeté',
    ];

    protected $fillable = [
        'prestation_id',
        'docusign_envelope_id',
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prestation extends Model
{
    use HasFactory , SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'artiste_id',
        'nom_structure_contractante',
        'nom_representant_legal_artiste',
        'contact_artiste',
        'contact_organisateur',
        'date_prestation',
        'heure_debut_prestation',
        'heure_fin_prevue',
        'lieu_prestation',
        'duree_effective_performance',
        'type_evenement',
        'nombre_sets_morceaux',
        'montant_total_cachet',
        'modalites_paiement',
        'montant_avance',
        'date_limite_paiement_solde',
        'frais_annexes_transport',
        'frais_annexes_hebergement',
        'frais_annexes_restauration',
        'frais_annexes_per_diem',
        'frais_annexes_autres',
        'materiel_fourni_organisateur',
        'materiel_apporte_artiste',
        'besoins_techniques',
        'droits_image',
        'mention_artiste_supports_communication',
        'interdiction_captation_audio_video',
        'clause_annulation',
        'responsabilite_force_majeure',
        'assurance_securite_lieu_par',
        'engagement_ponctualite_presence',
        // Les champs de signature sont retirés de ce modèle
        'observations_particulieres',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_prestation' => 'date',
        'heure_debut_prestation' => 'datetime', // Carbon object for time
        'heure_fin_prevue' => 'datetime', // Carbon object for time
        'date_limite_paiement_solde' => 'date',
        'frais_annexes_transport' => 'boolean',
        'frais_annexes_hebergement' => 'boolean',
        'frais_annexes_restauration' => 'boolean',
        'frais_annexes_per_diem' => 'boolean',
        'mention_artiste_supports_communication' => 'boolean',
        'engagement_ponctualite_presence' => 'boolean',
        'montant_total_cachet' => 'decimal:2',
        'montant_avance' => 'decimal:2',
    ];

    /**
     * Define the relationship with the Artiste model.
     * A Prestation belongs to one Artiste.
     */
    public function artiste(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Artiste::class);
    }

    /**
     * Define the relationship with the Contract model.
     * A Prestation can have many Contracts.
     */
    public function contrats(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Contrat::class);
    }
}


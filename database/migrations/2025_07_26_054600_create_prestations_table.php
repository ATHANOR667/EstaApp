<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('prestations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Artiste::class);

            // Informations Générales
            $table->string('nom_structure_contractante')->nullable();
            $table->string('nom_representant_legal_artiste')->nullable();
            $table->string('contact_artiste')->nullable(); // Téléphone, email
            $table->string('contact_organisateur')->nullable();

            // Détails de la prestation
            $table->date('date_prestation')->nullable();
            $table->time('heure_debut_prestation')->nullable();
            $table->time('heure_fin_prevue')->nullable();
            $table->text('lieu_prestation')->nullable(); // Adresse complète ou géolocalisation
            $table->integer('duree_effective_performance')->nullable(); // En minutes ou heures
            $table->string('type_evenement')->nullable(); // Concert, Mariage, Soirée privée, Festival, etc.
            $table->integer('nombre_sets_morceaux')->nullable();

            // Conditions financières
            $table->decimal('montant_total_cachet', 10, 2)->nullable();
            $table->string('modalites_paiement')->nullable(); // Avance + Solde, Paiement unique, autre
            $table->decimal('montant_avance', 10, 2)->nullable();
            $table->date('date_limite_paiement_solde')->nullable();
            $table->boolean('frais_annexes_transport')->nullable()->default(false);
            $table->boolean('frais_annexes_hebergement')->nullable()->default(false);
            $table->boolean('frais_annexes_restauration')->nullable()->default(false);
            $table->boolean('frais_annexes_per_diem')->nullable()->default(false);
            $table->text('frais_annexes_autres')->nullable(); // Champ texte libre

            // Spécificités techniques
            $table->text('materiel_fourni_organisateur')->nullable(); // Zone de texte / Liste à puces
            $table->text('materiel_apporte_artiste')->nullable(); // Zone de texte
            $table->text('besoins_techniques')->nullable(); // Son, lumière, scène, loge, etc.

            // Communication et promotion
            $table->string('droits_image')->nullable(); // Oui / Non / À définir
            $table->boolean('mention_artiste_supports_communication')->nullable()->default(false);
            $table->string('interdiction_captation_audio_video')->nullable(); // Oui / Non / Partielle

            // Clauses contractuelles (signatures retirées de Prestation)
            $table->text('clause_annulation')->nullable(); // Conditions, délais, pénalités
            $table->string('responsabilite_force_majeure')->nullable(); // Choix ou texte libre
            $table->string('assurance_securite_lieu_par')->nullable(); // Organisateur / Artiste / Autre
            $table->boolean('engagement_ponctualite_presence')->nullable()->default(false);
            // Les champs de signature sont retirés de cette table

            // Optionnel
            $table->text('observations_particulieres')->nullable(); // Zone de texte libre

            // Statut de la prestation (par défaut 'en cours de redaction')
            $table->string('status')->default('en cours de redaction'); // en cours de redaction, redigee

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestations');
    }
};

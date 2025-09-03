<?php

namespace App\Livewire\Admin\Calendar\Prestation;

use App\Models\Artiste;
use App\Models\Prestation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class PrestationFormModal extends Component
{

    public bool $showModal = false;
    public int|null $editingPrestationId = null;
    public string|null $initialDate = null;

    public array $form = [
        'artiste_id' => null,
        'nom_structure_contractante' => '',
        'nom_representant_legal_artiste' => '',
        'contact_artiste' => '',
        'contact_organisateur' => '',
        'date_prestation' => '',
        'heure_debut_prestation' => '',
        'heure_fin_prevue' => '',
        'lieu_prestation' => '',
        'duree_effective_performance' => null,
        'type_evenement' => '',
        'nombre_sets_morceaux' => null,
        'montant_total_cachet' => null,
        'modalites_paiement' => '',
        'montant_avance' => null,
        'date_limite_paiement_solde' => '',
        'frais_annexes_transport' => false,
        'frais_annexes_hebergement' => false,
        'frais_annexes_restauration' => false,
        'frais_annexes_per_diem' => false,
        'frais_annexes_autres' => '',
        'materiel_fourni_organisateur' => '',
        'materiel_apporte_artiste' => '',
        'besoins_techniques' => '',
        'droits_image' => '',
        'mention_artiste_supports_communication' => false,
        'interdiction_captation_audio_video' => '',
        'clause_annulation' => '',
        'responsabilite_force_majeure' => '',
        'assurance_securite_lieu_par' => '',
        'engagement_ponctualite_presence' => false,
        'observations_particulieres' => '',
        'status' => 'en cours de redaction',
    ];

    public string|null $overlappingWarning = null;
    public Collection $artistes;



    public function mount(): void
    {
        $this->form['date_prestation'] = Carbon::now()->format('Y-m-d');
        $this->artistes = Auth::guard('admin')->user()->artistes;

        if ($this->editingPrestationId) {
            $this->loadPrestation();
        }
    }

    public function updatedShowModal($value): void
    {
        if (!$value) {
            $this->resetForm();
        }
    }

    private function loadPrestation(): void
    {
        $prestation = Prestation::with('artiste')->findOrFail($this->editingPrestationId);

        // Assigner explicitement chaque champ avec une gestion des valeurs nulles
        $this->form['artiste_id'] = $prestation->artiste_id;
        $this->form['nom_structure_contractante'] = $prestation->nom_structure_contractante ?? '';
        $this->form['nom_representant_legal_artiste'] = $prestation->nom_representant_legal_artiste ?? '';
        $this->form['contact_artiste'] = $prestation->contact_artiste ?? '';
        $this->form['contact_organisateur'] = $prestation->contact_organisateur ?? '';

        $this->form['date_prestation'] = $prestation->date_prestation ? Carbon::parse($prestation->date_prestation)->format('Y-m-d') : '';
        $this->form['heure_debut_prestation'] = $prestation->heure_debut_prestation ? Carbon::parse($prestation->heure_debut_prestation)->format('H:i') : '';
        $this->form['heure_fin_prevue'] = $prestation->heure_fin_prevue ? Carbon::parse($prestation->heure_fin_prevue)->format('H:i') : '';
        $this->form['lieu_prestation'] = $prestation->lieu_prestation ?? '';
        $this->form['duree_effective_performance'] = $prestation->duree_effective_performance;
        $this->form['type_evenement'] = $prestation->type_evenement ?? '';
        $this->form['nombre_sets_morceaux'] = $prestation->nombre_sets_morceaux;

        $this->form['montant_total_cachet'] = $prestation->montant_total_cachet;
        $this->form['modalites_paiement'] = $prestation->modalites_paiement ?? '';
        $this->form['montant_avance'] = $prestation->montant_avance;
        $this->form['date_limite_paiement_solde'] = $prestation->date_limite_paiement_solde ? Carbon::parse($prestation->date_limite_paiement_solde)->format('Y-m-d') : '';

        $this->form['frais_annexes_transport'] = (bool) ($prestation->frais_annexes_transport ?? false);
        $this->form['frais_annexes_hebergement'] = (bool) ($prestation->frais_annexes_hebergement ?? false);
        $this->form['frais_annexes_restauration'] = (bool) ($prestation->frais_annexes_restauration ?? false);
        $this->form['frais_annexes_per_diem'] = (bool) ($prestation->frais_annexes_per_diem ?? false);
        $this->form['frais_annexes_autres'] = $prestation->frais_annexes_autres ?? '';

        $this->form['materiel_fourni_organisateur'] = $prestation->materiel_fourni_organisateur ?? '';
        $this->form['materiel_apporte_artiste'] = $prestation->materiel_apporte_artiste ?? '';
        $this->form['besoins_techniques'] = $prestation->besoins_techniques ?? '';

        $this->form['droits_image'] = $prestation->droits_image ?? '';
        $this->form['mention_artiste_supports_communication'] = (bool) ($prestation->mention_artiste_supports_communication ?? false);
        $this->form['interdiction_captation_audio_video'] = $prestation->interdiction_captation_audio_video ?? '';

        $this->form['clause_annulation'] = $prestation->clause_annulation ?? '';
        $this->form['responsabilite_force_majeure'] = $prestation->responsabilite_force_majeure ?? '';
        $this->form['assurance_securite_lieu_par'] = $prestation->assurance_securite_lieu_par ?? '';
        $this->form['engagement_ponctualite_presence'] = (bool) ($prestation->engagement_ponctualite_presence ?? false);

        $this->form['observations_particulieres'] = $prestation->observations_particulieres ?? '';
        $this->form['status'] = $prestation->status ?? 'en cours de redaction';
    }

    protected function rules()
    {
        return [
            'form.artiste_id' => 'required|exists:artistes,id', // artiste_id est maintenant requis
            'form.nom_structure_contractante' => 'required|string|max:255',
            'form.date_prestation' => 'required|date',
            'form.heure_debut_prestation' => 'required|date_format:H:i',
            //'form.heure_fin_prevue' => 'required|date_format:H:i|after:form.heure_debut_prestation',
            'form.lieu_prestation' => 'required|string|max:255',
            'form.type_evenement' => 'required|string|max:255',
            'form.status' => 'required|string|in:en cours de redaction,validee',
            'form.nom_representant_legal_artiste' => 'nullable|string|max:255',
            'form.contact_artiste' => 'nullable|string|max:255',
            'form.contact_organisateur' => 'nullable|string|max:255',
            'form.duree_effective_performance' => 'nullable|integer',
            'form.nombre_sets_morceaux' => 'nullable|integer',
            'form.montant_total_cachet' => 'nullable|numeric',
            'form.modalites_paiement' => 'nullable|string',
            'form.montant_avance' => 'nullable|numeric',
            'form.date_limite_paiement_solde' => 'nullable|date',
            'form.frais_annexes_transport' => 'boolean',
            'form.frais_annexes_hebergement' => 'boolean',
            'form.frais_annexes_restauration' => 'boolean',
            'form.frais_annexes_per_diem' => 'boolean',
            'form.frais_annexes_autres' => 'nullable|string',
            'form.materiel_fourni_organisateur' => 'nullable|string',
            'form.materiel_apporte_artiste' => 'nullable|string',
            'form.besoins_techniques' => 'nullable|string',
            'form.droits_image' => 'nullable|string',
            'form.mention_artiste_supports_communication' => 'boolean',
            'form.interdiction_captation_audio_video' => 'nullable|string',
            'form.clause_annulation' => 'nullable|string',
            'form.responsabilite_force_majeure' => 'nullable|string',
            'form.assurance_securite_lieu_par' => 'nullable|string',
            'form.engagement_ponctualite_presence' => 'boolean',
            'form.observations_particulieres' => 'nullable|string',
        ];
    }

    protected $messages = [
        'form.artiste_id.required' => 'La sélection de l\'artiste est obligatoire.',
        'form.artiste_id.exists' => 'L\'artiste sélectionné n\'est pas valide.',
        'form.nom_structure_contractante.required' => 'Le nom de la structure contractante est obligatoire.',
        'form.date_prestation.required' => 'La date de la prestation est obligatoire.',
        'form.date_prestation.date' => 'La date de la prestation doit être une date valide.',
        'form.heure_debut_prestation.required' => 'L\'heure de début est obligatoire.',
        'form.heure_debut_prestation.date_format' => 'L\'heure de début doit être au format HH:MM.',
        'form.heure_fin_prevue.required' => 'L\'heure de fin est obligatoire.',
        'form.heure_fin_prevue.date_format' => 'L\'heure de fin doit être au format HH:MM.',
        'form.heure_fin_prevue.after' => 'L\'heure de fin doit être après l\'heure de début.',
        'form.lieu_prestation.required' => 'Le lieu de la prestation est obligatoire.',
        'form.type_evenement.required' => 'Le type d\'événement est obligatoire.',
        'form.status.required' => 'Le statut de la prestation est obligatoire.',
        'form.status.in' => 'Le statut de la prestation n\'est pas valide.',
    ];


    #[On('open-prestation-form')]
    public function openModalForCreate(string $date = null): void
    {
        Gate::authorize('create-prestation');

        $this->resetForm();
        $this->editingPrestationId = null;
        $this->initialDate = $date;
        if ($this->initialDate) {
            $this->form['date_prestation'] = $this->initialDate;
        }
        $this->showModal = true;
    }

    #[On('edit-prestation')]
    public function openModalForEdit(int $prestationId): void
    {
        Gate::authorize('edit-prestation');

        $this->resetForm();
        $this->editingPrestationId = $prestationId;
        $this->initialDate = null;
        $this->loadPrestation();
        $this->showModal = true;
    }

    public function savePrestation(): void
    {

        Gate::any(['create-prestation', 'edit-prestation']);
        $this->resetErrorBag();
        $this->overlappingWarning = null;

        $this->validate();

        $date = $this->form['date_prestation'];
        $heureDebut = $this->form['heure_debut_prestation'];
        $heureFin = $this->form['heure_fin_prevue'];

        $artiste = Artiste::find($this->form['artiste_id']);
        $nomArtiste = $artiste ? $artiste->nom : 'Artiste inconnu';

        // Vérification des chevauchements pour le même artiste
        $querySameArtist = Prestation::where('date_prestation', $date)
            ->where('artiste_id', $this->form['artiste_id'])
            ->where(function ($q) use ($heureDebut, $heureFin) {
                $q->where(function ($q2) use ($heureDebut, $heureFin) {
                    $q2->where('heure_debut_prestation', '<', $heureFin)
                        ->where('heure_fin_prevue', '>', $heureDebut);
                });
            });

        if ($this->editingPrestationId) {
            $querySameArtist->where('id', '!=', $this->editingPrestationId);
        }

        if ($querySameArtist->exists()) {
            session()->flash('error', 'Impossible de créer ou modifier cette prestation : Une autre prestation pour le même artiste (' . $nomArtiste . ') chevauche cette période.');
            return;
        }

        // Vérification des chevauchements avec d'autres artistes (pour l'avertissement)
        $queryOtherArtists = Prestation::where('date_prestation', $date)
            ->where('artiste_id', '!=', $this->form['artiste_id'])
            ->where(function ($q) use ($heureDebut, $heureFin) {
                $q->where(function ($q2) use ($heureDebut, $heureFin) {
                    $q2->where('heure_debut_prestation', '<', $heureFin)
                        ->where('heure_fin_prevue', '>', $heureDebut);
                });
            });

        if ($this->editingPrestationId) {
            $queryOtherArtists->where('id', '!=', $this->editingPrestationId);
        }

        $overlappingOtherPrestations = $queryOtherArtists->get();

        if ($overlappingOtherPrestations->isNotEmpty()) {
            // Récupérer les noms des artistes qui chevauchent via leur relation artiste
            $overlappingArtistsNames = $overlappingOtherPrestations->map(function($p) {
                return $p->artiste ? $p->artiste->nom : 'Artiste inconnu';
            })->unique()->implode(', ');
            $this->overlappingWarning =
                'Attention : ' . ($overlappingOtherPrestations->count() > 1

                    ? 'D\'autres artistes (' . $overlappingArtistsNames . ') ont des prestations'

                    : 'L\'artiste ' . $overlappingArtistsNames . ' a une prestation') .
                      ' qui chevauche cette période.';
        }

        try {
            if ($this->editingPrestationId) {
                $prestation = Prestation::findOrFail($this->editingPrestationId);
                $prestation->update($this->form);
                session()->flash('success', 'Prestation mise à jour avec succès !');
            } else {
                Prestation::create($this->form);
                session()->flash('success', 'Prestation créée avec succès !');
            }
            $this->closeModal();
            $this->dispatch('refreshCalendar');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->editingPrestationId = null;
        $this->initialDate = null;
        $this->overlappingWarning = null;

        $this->form = [
            'artiste_id' => null,
            'nom_structure_contractante' => '',
            'nom_representant_legal_artiste' => '',
            'contact_artiste' => '',
            'contact_organisateur' => '',
            'date_prestation' => Carbon::now()->format('Y-m-d'),
            'heure_debut_prestation' => '',
            'heure_fin_prevue' => '',
            'lieu_prestation' => '',
            'duree_effective_performance' => null,
            'type_evenement' => '',
            'nombre_sets_morceaux' => null,
            'montant_total_cachet' => null,
            'modalites_paiement' => '',
            'montant_avance' => null,
            'date_limite_paiement_solde' => '',
            'frais_annexes_transport' => false,
            'frais_annexes_hebergement' => false,
            'frais_annexes_restauration' => false,
            'frais_annexes_per_diem' => false,
            'frais_annexes_autres' => '',
            'materiel_fourni_organisateur' => '',
            'materiel_apporte_artiste' => '',
            'besoins_techniques' => '',
            'droits_image' => '',
            'mention_artiste_supports_communication' => false,
            'interdiction_captation_audio_video' => '',
            'clause_annulation' => '',
            'responsabilite_force_majeure' => '',
            'assurance_securite_lieu_par' => '',
            'engagement_ponctualite_presence' => false,
            'observations_particulieres' => '',
            'status' => 'en cours de redaction',
        ];
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.calendar.prestation.prestation-form-modal');
    }
}

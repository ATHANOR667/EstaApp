<?php

namespace App\Livewire\SuperAdmin\ManageTeams;

use App\Models\Artiste;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ArtisteForm extends Component
{
    use WithFileUploads;

    public $artisteId;
    public string $nom = '';
    public ?string $photo = null;
    public $newPhoto;
    public ?string $couleur = null;
    public bool $showModal = false;

    protected $listeners = ['openCreateModal', 'openEditModal'];

    protected function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'newPhoto' => ['nullable', 'image', 'max:2048'],
            'couleur' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ];
    }

    public function openCreateModal(): void
    {
        $this->reset(['artisteId', 'nom', 'photo', 'newPhoto', 'couleur']);
        $this->showModal = true;
    }

    public function openEditModal(int $artisteId): void
    {
        $artiste = Artiste::find($artisteId);

        if (!$artiste) {
            session()->flash('error', 'Artiste non trouvé.');
            return;
        }

        $this->artisteId = $artisteId;
        $this->nom = $artiste->nom;
        $this->photo = $artiste->photo;
        $this->couleur = $artiste->couleur;
        $this->reset('newPhoto');
        $this->showModal = true;
    }

    public function saveArtiste(): void
    {
        $this->validate();

        try {
            $data = [
                'nom' => $this->nom,
                'couleur' => $this->couleur,
            ];

            if ($this->newPhoto) {
                if ($this->photo) {
                    Storage::disk('public')->delete($this->photo);
                }
                $data['photo'] = $this->newPhoto->store('artiste_photos', 'public');
            }

            if ($this->artisteId) {
                $artiste = Artiste::findOrFail($this->artisteId);
                $artiste->update($data);
                session()->flash('success', 'Artiste "' . $this->nom . '" mis à jour avec succès !');
            } else {
                Artiste::create($data);
                session()->flash('success', 'Artiste "' . $this->nom . '" créé avec succès !');
            }

            $this->dispatch('artisteUpdated');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'enregistrement de l\'artiste : ' . $e->getMessage());
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['artisteId', 'nom', 'photo', 'newPhoto', 'couleur']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.super-admin.manage-teams.artiste-form');
    }
}

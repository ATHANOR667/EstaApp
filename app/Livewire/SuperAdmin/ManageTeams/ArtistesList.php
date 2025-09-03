<?php

namespace App\Livewire\SuperAdmin\ManageTeams;

use App\Models\Artiste;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class ArtistesList extends Component
{
    use WithPagination;

    public ?Artiste $selectedArtiste = null;
    public string $search = '';

    protected $listeners = ['artisteUpdated' => '$refresh'];

    public function selectArtiste(int $artisteId): void
    {
        $this->selectedArtiste = Artiste::find($artisteId);
        $this->dispatch('artisteSelected', $artisteId);
    }

    public function openCreateModal(): void
    {
        $this->dispatch('openCreateModal');
    }

    public function openEditModal(int $artisteId): void
    {
        $this->dispatch('openEditModal', $artisteId);
    }

    public function deleteArtiste(int $artisteId): void
    {
        try {
            $artiste = Artiste::findOrFail($artisteId);
            if ($artiste->photo) {
                Storage::disk('public')->delete($artiste->photo);
            }
            $artiste->delete();
            session()->flash('success', 'Artiste "' . $artiste->nom . '" supprimé avec succès !');
            if ($this->selectedArtiste && $this->selectedArtiste->id === $artisteId) {
                $this->selectedArtiste = null;
                $this->dispatch('artisteSelected', null);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression de l\'artiste : ' . $e->getMessage());
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $artistes = Artiste::query()
            ->when($this->search, fn($query) => $query->where('nom', 'like', '%' . $this->search . '%'))
            ->orderBy('nom')
            ->paginate(10);

        return view('livewire.super-admin.manage-teams.artistes-list', [
            'artistes' => $artistes,
        ]);
    }
}

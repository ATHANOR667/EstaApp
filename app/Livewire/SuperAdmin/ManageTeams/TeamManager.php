<?php

namespace App\Livewire\SuperAdmin\ManageTeams;

use App\Models\Admin;
use App\Models\Artiste;
use Livewire\Component;
use Livewire\WithPagination;

class TeamManager extends Component
{
    use WithPagination;

    public ?Artiste $artiste = null;
    public string $searchAdmin = '';
    public array $adminsToAdd = [];

    protected $listeners = ['artisteSelected' => 'loadArtiste'];

    public function loadArtiste(?int $artisteId): void
    {
        $this->artiste = $artisteId ? Artiste::with('admins')->find($artisteId) : null;
        $this->searchAdmin = '';
        $this->adminsToAdd = [];
        $this->resetPage();
    }

    public function toggleAdminToAdd(int $adminId): void
    {
        if (in_array($adminId, $this->adminsToAdd)) {
            $this->adminsToAdd = array_diff($this->adminsToAdd, [$adminId]);
        } else {
            $this->adminsToAdd[] = $adminId;
        }
    }

    public function addAdminToTeam(): void
    {
        if (!$this->artiste) {
            session()->flash('error', 'Aucun artiste sélectionné.');
            return;
        }

        try {
            $this->artiste->admins()->syncWithoutDetaching($this->adminsToAdd);
            $this->adminsToAdd = [];
            $this->artiste->refresh();
            session()->flash('success', 'Administrateurs ajoutés à l\'équipe avec succès !');
            $this->dispatch('teamUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout des administrateurs : ' . $e->getMessage());
        }
    }

    public function removeAdminFromTeam(int $adminId): void
    {
        if (!$this->artiste) {
            session()->flash('error', 'Aucun artiste sélectionné.');
            return;
        }

        try {
            $this->artiste->admins()->detach($adminId);
            $this->artiste->refresh();
            session()->flash('success', 'Administrateur retiré de l\'équipe avec succès !');
            $this->dispatch('teamUpdated');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du retrait de l\'administrateur : ' . $e->getMessage());
        }
    }

    public function viewAdminDetails(int $adminId): void
    {
        $this->dispatch('openAdminProfileCard', $adminId);
    }

    public function updatedSearchAdmin(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $availableAdmins = $this->artiste
            ? Admin::query()
                ->whereNotIn('id', $this->artiste->admins->pluck('id'))
                ->when($this->searchAdmin, fn($query) => $query->where(function ($q) {
                    $q->where('nom', 'like', '%' . $this->searchAdmin . '%')
                        ->orWhere('prenom', 'like', '%' . $this->searchAdmin . '%')
                        ->orWhere('matricule', 'like', '%' . $this->searchAdmin . '%');
                }))
                ->orderBy('nom')
                ->paginate(10)
            : collect();

        return view('livewire.super-admin.manage-teams.team-manager', [
            'availableAdmins' => $availableAdmins,
        ]);
    }

    private function hex2rgb(string $hex): string
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return "$r, $g, $b";
    }
}

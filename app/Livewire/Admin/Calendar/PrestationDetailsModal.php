<?php

namespace App\Livewire\Admin\Calendar;

use Livewire\Attributes\On;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Prestation;
use Illuminate\Support\Collection;

class PrestationDetailsModal extends Component
{
    public bool $showModal = false;
    public string|null $selectedDate = null;
    public int|null $selectedPrestationId = null;

    public Prestation $prestation;
    public bool $hasAcceptedContrat = false;


    #[On('open-prestation-details')]
    public function openModal(string $date, int $prestationId ): void
    {
        $this->selectedDate = $date;
        $this->selectedPrestationId = $prestationId;
        $this->showModal = true;
        $this->prestation = Prestation::with(['artiste', 'contrats'])->findOrFail($prestationId);
        $this->loadPrestationDetails();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $this->loadPrestationDetails();
        return view('livewire.admin.calendar.prestation-details-modal');
    }

    public function updated($propertyName): void
    {
        if ($propertyName === 'showModal' && $this->showModal) {
            $this->loadPrestationDetails();
        }
        if (($propertyName === 'selectedDate' || $propertyName === 'selectedPrestationId') && $this->showModal) {
            $this->loadPrestationDetails();
        }
    }

    private function loadPrestationDetails(): void
    {
        if ($this->selectedPrestationId) {
            $this->prestation = Prestation::with(['artiste', 'contrats'])->findOrFail($this->selectedPrestationId);
            $this->hasAcceptedContrat = $this->prestation->contrats->where('status', 'accepté')->isNotEmpty();
        } else {
            $this->prestation = new Prestation();
            $this->hasAcceptedContrat = false;
        }
    }



    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selectedDate = null;
        $this->selectedPrestationId = null;
        $this->prestation = new Prestation();
        $this->hasAcceptedContrat = false;
    }

    public function editPrestation(): void
    {
        $this->dispatch('edit-prestation', prestationId: $this->prestation->id);
    }

    public function deletePrestation(): void
    {
        try {

            if ($this->prestation->contrats->where('status', 'accepté')->isNotEmpty()) {
                session()->flash('error', 'Impossible de supprimer cette prestation : elle a des contrats acceptés.');
                return;
            }

            $this->prestation->delete();
            $this->closeModal();
            session()->flash('success', 'Prestation supprimée avec succès !');
            $this->dispatch('refreshCalendar');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de la suppression : ' . $e->getMessage());
        }    }

    public function openContratList(): void
    {
        $this->dispatch('open-contrat-list', prestationId: $this->prestation->id);
    }


}

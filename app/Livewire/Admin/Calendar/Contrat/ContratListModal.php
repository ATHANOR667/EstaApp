<?php

namespace App\Livewire\Admin\Calendar\Contrat;

use App\Models\Contrat;
use App\Models\Prestation;
use App\Services\DocuSignService;
use Barryvdh\DomPDF\Facade\Pdf;
use DocuSign\eSign\Client\ApiClient;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\On;
use Livewire\Component;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ContratListModal extends Component
{
    public bool $showContratListModal = false;
    public int|null $prestationId = null;
    public Prestation $prestation;
    public Collection|\Illuminate\Support\Collection $contrats;
    public string $customMessage;

    #[On('open-contrat-list')]
    public function openModal(int $prestationId = null): void
    {
        Gate::authorize('see-contrat');

        if (!$prestationId) {
            session()->flash('error', 'Aucune prestation sélectionnée.');
            return;
        }
        $this->prestationId = $prestationId;
        $this->showContratListModal = true;
        $this->loadContrats();
    }

    public function mount(): void
    {
        $this->loadContrats();
    }

    public function updatedShowModal($value): void
    {
        if ($value && $this->prestationId) {
            $this->loadContrats();
        } else {
            $this->contrats = collect();
            $this->prestation = new Prestation();
        }
    }

    public function loadContrats(): void
    {
        if ($this->prestationId) {
            $this->prestation = Prestation::with('contrats', 'artiste')
                ->findOrFail($this->prestationId);
            $this->contrats = $this->prestation
                ->contrats()
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->contrats = collect();
            $this->prestation = new Prestation();
        }
    }

    public function openContratForm($prestationId): void
    {
        Gate::authorize('create-contrat');

        if (!$prestationId) {
            session()->flash('error', 'Aucune prestation sélectionnée.');
            return;
        }
        $this->dispatch('open-contrat-form', prestationId: $prestationId);
    }

    public function openContratFormWithAi($prestationId): void
    {
        Gate::authorize('create-contrat');

        if (!$prestationId) {
            session()->flash('error', 'Aucune prestation sélectionnée.');
            return;
        }
        $this->dispatch('open-contrat-form', prestationId: $prestationId, generateWithAi: true);
    }

    public function viewContrat($contratId): void
    {
        Gate::authorize('see-contrat');
        if (!$contratId) {
            session()->flash('error', 'Aucun contrat sélectionné.');
            return;
        }
        $this->dispatch('view-contrat', contratId: $contratId);
    }

    public function editContrat($contratId): void
    {
        Gate::authorize('edit-contrat');

        if (!$contratId) {
            session()->flash('error', 'Aucun contrat sélectionné.');
            return;
        }
        $this->dispatch('edit-contrat', contratId: $contratId);
    }

    public function closeModal(): void
    {
        $this->showContratListModal = false;
        $this->prestationId = null;
        $this->contrats = collect();
        $this->prestation = new Prestation();
        $this->customMessage = '';
    }

    public function sendContract(string $contratId): void
    {
        Gate::authorize('send-contrat');
        $this->dispatch('send-contrat', id: $contratId);
    }

    public function downloadPdf(Contrat $contrat  ): \SplFileObject | \Symfony\Component\HttpFoundation\StreamedResponse | null
    {
        Gate::authorize('download-contrat');

        try {
            if ($contrat->status === 'signed' && $contrat->docusign_envelope_id) {

                $docusignService = new  DocuSignService();
                return $docusignService->downloadSignedDocument($contrat);

            } else {
                return $contrat->download();
            }
        } catch (Exception $e) {
            session()->flash('error', 'Problème interne lors de la génération de votre PDF.');
            Log::error('Erreur lors de la génération/récupération du PDF', ['error' => $e->getMessage()]);
            return null ;
        }
    }

    #[On('refreshContratList')]
    public function refresh(): void
    {
        $this->loadContrats();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.calendar.contrat.contrat-list-modal');
    }
}

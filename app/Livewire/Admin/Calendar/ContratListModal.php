<?php

namespace App\Livewire\Admin\Calendar;

use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Prestation;
use App\Models\Contrat;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use DocuSign\eSign\Client\ApiClient;
use Illuminate\Support\Facades\URL;

class ContratListModal extends Component
{
    public bool $showContratListModal = false;
    public int|null $prestationId = null;
    public Prestation $prestation;
    public Collection|\Illuminate\Support\Collection $contrats;
    public string $customMessage;

    #[On('open-contrat-list')]
    public function openModal(int $prestationId): void
    {
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
            $this->prestation = Prestation::with('contrats', 'artiste')->findOrFail($this->prestationId);
            $this->contrats = $this->prestation->contrats()->orderBy('created_at', 'desc')->get();
        } else {
            $this->contrats = collect();
            $this->prestation = new Prestation();
        }
    }

    public function openContratForm($prestationId): void
    {
        if (!$prestationId) {
            session()->flash('error', 'Aucune prestation sélectionnée.');
            return;
        }
        $this->dispatch('open-contrat-form', prestationId: $prestationId);
    }

    public function openContratFormWithAi($prestationId): void
    {
        if (!$prestationId) {
            session()->flash('error', 'Aucune prestation sélectionnée.');
            return;
        }
        $this->dispatch('open-contrat-form', prestationId: $prestationId, generateWithAi: true);
    }

    public function viewContrat($contratId): void
    {
        if (!$contratId) {
            session()->flash('error', 'Aucun contrat sélectionné.');
            return;
        }
        $this->dispatch('view-contrat', contratId: $contratId);
    }

    public function editContrat($contratId): void
    {
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

    public function sendContract(int $contratId): void
    {
        $this->dispatch('send-contrat', id: $contratId);
    }

    public function downloadPdf(Contrat $contrat): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        try {
            if ($contrat->status === 'signed' && $contrat->docusign_envelope_id) {
                // Récupérer le PDF signé depuis DocuSign
                $apiClient = new ApiClient();
                $apiClient->getOAuth()->setOAuthBasePath(config('docusign.oauth_base_path'));
                $accessToken = $apiClient->requestJWTUserToken(
                    config('docusign.client_id'),
                    config('docusign.user_id'),
                    config('docusign.key_path'),
                    config('docusign.scope')
                )[0]['access_token'];
                $apiClient->getConfig()->setAccessToken($accessToken);

                $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($apiClient);
                $pdf = $envelopeApi->getDocument(config('docusign.account_id'), $contrat->docusign_envelope_id, '1');

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf;
                }, 'contrat_' . $contrat->id . '_signed.pdf');
            } else {
                // Générer le PDF local
                $qrCodeUrl = URL::temporarySignedRoute(
                    'contrats.download_pdf',
                    now()->addDays(7),
                    ['contrat' => $contrat->id]
                );
                $qrCodeSvg = QrCode::size(150)->generate($qrCodeUrl)->toHtml();
                $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

                $pdf = Pdf::loadView('pdf.view_contract', [
                    'contrat' => $contrat,
                    'qrCodeSvg' => $qrCodeBase64,
                    'dateEmission' => now()->format('d/m/Y'),
                ])->setPaper('A4', 'portrait');

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->stream();
                }, 'contrat_' . $contrat->id . '.pdf');
            }
        } catch (Exception $e) {
            session()->flash('error', 'Problème interne lors de la génération de votre PDF.');
            Log::error('Erreur lors de la génération/récupération du PDF', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    #[On('refreshContratList')]
    public function refresh(): void
    {
        $this->loadContrats();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.calendar.contrat-list-modal');
    }
}

<?php

namespace App\Livewire\Admin\Calendar\Contrat;

use App\Models\Contrat;
use App\Models\Prestation;
use Barryvdh\DomPDF\Facade\Pdf;
use DocuSign\eSign\Client\ApiClient;
use Exception;
use Illuminate\Database\Eloquent\Collection;
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

    public function downloadPdf(Contrat $contrat): \SplFileObject | \Symfony\Component\HttpFoundation\StreamedResponse | null
    {
        try {
            if ($contrat->status === 'signed' && $contrat->docusign_envelope_id) {
                // Configurer l'API DocuSign
                $apiClient = new ApiClient();
                $apiClient->getConfig()->setHost('https://demo.docusign.net/restapi');

                // Validation du fichier clé privée
                $keyPath = base_path(config('services.docusign.key_path'));
                $keyContent = file_get_contents($keyPath);


                $apiClient->getOAuth()->setOAuthBasePath(config('services.docusign.oauth_base_path'));
                $tokenResponse = $apiClient->requestJWTUserToken(
                    config('services.docusign.client_id'),
                    config('services.docusign.user_id'),
                    $keyContent,
                    config('services.docusign.scope')
                );
                $accessToken = $tokenResponse[0]['access_token'];
                $apiClient->getConfig()->setAccessToken($accessToken);

                $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($apiClient);

                $pdf = $envelopeApi->getDocument(
                    config('services.docusign.account_id'),
                    $contrat->docusign_document_id ,
                    $contrat->docusign_envelope_id,
                );

                // Streamer le PDF
                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf;
                }, 'contrat_' . $contrat->id . '_signed.pdf', [
                    'Content-Type' => 'application/pdf',
                ]);

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

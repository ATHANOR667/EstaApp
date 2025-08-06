<?php

namespace App\Livewire\Admin\Calendar;

use App\Events\ContractContentGenerated;
use App\Events\ContractContentGenerationFailed;
use App\Jobs\GenerateContractContent;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Contrat;

class ContratFormModal extends Component
{
    public bool $showModal = false;
    public bool $showSendOptions = false;
    public int|null $prestationId = null;
    public int|null $contratId = null;

    public array $form = [
        'content' => '',
        'status' => 'draft',
    ];
    public bool $isGenerating = false;
    public bool $isViewing = false;

    public function handleContentGenerated($data): void
    {
        Log::info('Contenu généré reçu', ['prestationId' => $this->prestationId, 'content_length' => strlen($data['content'])]);
        $this->form['content'] = $data['content'];
        $this->isGenerating = false;
        session()->flash('success', 'Contenu généré avec succès !');
    }

    public function handleContentGenerationFailed($data): void
    {
        Log::error('Échec de la génération IA', ['error' => $data['error'], 'prestationId' => $this->prestationId]);
        session()->flash('error', 'La génération a échoué. Veuillez réessayer.');
        $this->isGenerating = false;
        $this->dispatch('refresh');
    }

    #[On('open-contrat-form')]
    public function openModal($prestationId, $generateWithAi = false): void
    {
        Log::info('Ouverture de ContratFormModal', ['prestationId' => $prestationId]);
        $this->resetForm();
        $this->prestationId = $prestationId;
        $this->showModal = true;
        $this->resetErrorBag();
        session()->forget('error');

        if ($generateWithAi) {
            $this->generateContent();
        }

        $this->dispatch('reset-quill');
    }

    #[On('view-contrat')]
    public function viewContrat(int $contratId): void
    {
        $contrat = Contrat::findOrFail($contratId);
        $this->contratId = $contratId;
        $this->prestationId = $contrat->prestation_id;
        $this->form['content'] = $contrat->content;
        $this->form['status'] = $contrat->status;
        $this->isViewing = true;
        $this->isGenerating = false;
        $this->showSendOptions = false;
        $this->showModal = true;
        $this->resetErrorBag();
        session()->forget('error');
        $this->dispatch('reset-quill');
    }

    #[On('send-contrat')]
    public function sendContrat(int $id): void
    {
        $this->showSendOptions = true;
        $contrat = Contrat::findOrFail($id);
        $this->contratId = $id;
        $this->prestationId = $contrat->prestation_id;
        $this->form['content'] = $contrat->content;
        $this->form['status'] = $contrat->status;
        $this->isViewing = true;
        $this->isGenerating = false;
        $this->showModal = true;
        $this->resetErrorBag();
        session()->forget('error');
        $this->dispatch('reset-quill');
    }

    #[On('edit-contrat')]
    public function editContrat(int $contratId): void
    {
        $contrat = Contrat::findOrFail($contratId);
        $this->contratId = $contratId;
        $this->prestationId = $contrat->prestation_id;
        $this->form['content'] = $contrat->content;
        $this->form['status'] = $contrat->status;
        $this->isViewing = false;
        $this->isGenerating = false;
        $this->showSendOptions = false;
        $this->showModal = true;
        $this->resetErrorBag();
        session()->forget('error');
        $this->dispatch('reset-quill');
    }

    public function generateContent(): void
    {
        if (!$this->prestationId || !\App\Models\Prestation::findOrFail($this->prestationId)->exists()) {
            Log::error('Aucune prestation valide pour la génération', ['prestationId' => $this->prestationId]);
            session()->flash('error', 'Aucune prestation valide sélectionnée.');
            $this->isGenerating = false;
            return;
        }

        $this->isGenerating = true;
        $this->showSendOptions = false;
        $this->form['content'] = '';
        $this->dispatch('reset-quill');
        Log::info('Début de la génération IA', ['prestationId' => $this->prestationId]);

        try {
            GenerateContractContent::dispatch($this->prestationId, $this->getId());
            Log::info('Job IA dispatché', ['prestationId' => $this->prestationId]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du dispatch IA', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de la génération : ' . $e->getMessage());
            $this->isGenerating = false;
            $this->dispatch('refresh');
        }
    }

    public function saveLogic($content = null): void
    {
        Log::info('Appel de la méthode saveContrat', ['prestationId' => $this->prestationId, 'contratId' => $this->contratId, 'contentProvided' => $content !== null]);
        if (!$this->prestationId || !\App\Models\Prestation::where('id', $this->prestationId)->exists()) {
            Log::error('Prestation ID manquant ou invalide', ['prestationId' => $this->prestationId]);
            session()->flash('error', 'Aucune prestation valide sélectionnée.');
            return;
        }
        if ($content !== null) {
            $this->form['content'] = $content;
            Log::info('Contenu mis à jour depuis le front-end', ['contentLength' => strlen($content)]);
        }
        $this->validate([
            'form.content' => 'required|string',
            'form.status' => 'required|in:draft',
        ], [
            'form.content.required' => 'Le contenu du contrat est requis.',
            'form.status.required' => 'Le statut du contrat est requis.',
            'form.status.in' => 'Le statut doit être "draft".',
        ]);
        try {
            Log::info('Tentative de sauvegarde', ['prestationId' => $this->prestationId, 'contratId' => $this->contratId]);
            if ($this->contratId) {
                $contrat = Contrat::findOrFail($this->contratId);
                $contrat->update([
                    'content' => $this->form['content'],
                    'status' => $this->form['status'],
                ]);
                Log::info('Contrat mis à jour', ['contratId' => $this->contratId]);
            } else {
                $contrat = Contrat::create([
                    'prestation_id' => $this->prestationId,
                    'content' => $this->form['content'],
                    'status' => $this->form['status'],
                ]);
                $this->contratId = $contrat->id;
                Log::info('Contrat créé', ['contratId' => $contrat->id]);
            }
            session()->flash('success', 'Contrat sauvegardé avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function saveContrat($content = null): void
    {
        $this->saveLogic($content);
        $this->closeModal();
    }

    public function saveAndSend(): void
    {
        $this->saveLogic();
        if (!session()->has('error')) {
            $this->showSendOptions = true;
        }
    }

    private function prepareSend(string $method): void
    {
        if (Contrat::where('prestation_id', $this->prestationId)->where('status', 'pending')->exists()) {
            session()->flash('error', 'Un autre contrat est en attente de signature pour cette prestation.');
            Log::error('Envoi bloqué : contrat en attente', ['prestationId' => $this->prestationId]);
            return;
        }

        $this->dispatch('send-by-' . $method, contratId: $this->contratId, method: $method);
    }

    public function sendByMail(): void
    {
        $this->prepareSend('email');
    }

    public function sendByWhatsApp(): void
    {
        $this->prepareSend('whatsapp');
    }

    public function sendBySMS(): void
    {
        $this->prepareSend('sms');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $id = $this->prestationId;
        $this->resetForm();
        $this->resetErrorBag();
        session()->forget('error');
        $this->dispatch('reset-quill');
        $this->dispatch('open-contrat-list', prestationId: $id);
    }

    public function deleteContrat(): void
    {
        $contrat = Contrat::findOrFail($this->contratId);
        if (in_array($contrat->status, ['signed', 'rejected'])) {
            session()->flash('error', 'Impossible de supprimer un contrat signé ou rejeté.');
            return;
        }
        $contrat->delete();
        session()->flash('success', 'Contrat supprimé avec succès !');
    }

    private function resetForm(): void
    {
        $this->form = [
            'content' => '',
            'status' => 'draft',
        ];
        $this->contratId = null;
        $this->prestationId = null;
        $this->isGenerating = false;
        $this->isViewing = false;
        $this->showSendOptions = false;
    }

    #[On('refresh')]
    public function refresh()
    {
        // Forcer le rafraîchissement
    }

    #[On('quill-content-updated')]
    public function updateContentFromQuill($content): void
    {
        Log::info('Mise à jour du contenu depuis Quill', ['contentLength' => strlen($content)]);
        $this->form['content'] = $content;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.calendar.contrat-form-modal');
    }
}

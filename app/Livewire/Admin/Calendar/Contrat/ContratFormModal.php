<?php

namespace App\Livewire\Admin\Calendar\Contrat;

use App\Jobs\GenerateContractContent;
use App\Models\Contrat;
use Illuminate\Support\Facades\Auth ;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class ContratFormModal extends Component
{
    public bool $showModal = false;
    public bool $showSendOptions = false;
    public int|null $prestationId = null;
    public string|null $contratId = null;
    public int|null $userId = null;

    public array $form = [
        'content' => '',
        'status' => 'draft',
    ];
    public bool $isGenerating = false;
    public bool $isViewing = false;

    public function mount(): void
    {
        $this->userId = Auth::guard('admin')->id();
    }

    public function getListeners(): array
    {
        return [
            "echo-private:contrat-form-modal.{$this->userId},ContractContentGenerated" => 'handleContentGenerated',
            "echo-private:contrat-form-modal.{$this->userId},ContractContentGenerationFailed" => 'handleContentGenerationFailed',
        ];
    }

    public function handleContentGenerated(array $data): void
    {
        if (isset($data['cacheKey'])) {
            $htmlContent = Cache::get($data['cacheKey']);
            if ($htmlContent === null) {
                Log::error('Contenu non trouvé dans le cache', ['cache_key' => $data['cacheKey']]);
                session()->flash('error', 'Erreur : Contenu non trouvé dans le cache.');
                $this->isGenerating = false;
                return;
            }

            Log::info('Contenu récupéré depuis le cache', [
                'prestationId' => $this->prestationId,
                'content_length' => strlen($htmlContent),
                'content_preview' => substr($htmlContent, 0, 200),
            ]);

            $this->form['content'] = $htmlContent;
            $this->isGenerating = false;
            session()->flash('success', 'Contenu généré avec succès !');
            $this->dispatch('quill-content-updated', content: $htmlContent);
        } else {
            Log::error('Clé "cacheKey" manquante dans l\'événement', ['event' => $data]);
            $this->isGenerating = false;
            session()->flash('error', 'Erreur : Clé de cache non reçue');
        }
    }

    public function handleContentGenerationFailed(array $data): void
    {
        session()->flash('error', $data['error'] ?? 'La génération a échoué. Veuillez réessayer.');
        $this->isGenerating = false;
    }

    public function generateContent(): void
    {
        Gate::authorize('create-contrat');

        if (!$this->prestationId || !\App\Models\Prestation::find($this->prestationId)) {
            Log::error('Aucune prestation valide pour la génération', [
                'prestationId' => $this->prestationId,
            ]);
            session()->flash('error', 'Aucune prestation valide sélectionnée.');
            $this->isGenerating = false;
            return;
        }

        $this->isGenerating = true;
        $this->showSendOptions = false;
        $this->form['content'] = '';
        $this->dispatch('reset-quill');

        try {
            GenerateContractContent::dispatch($this->prestationId, $this->userId);

        } catch (\Exception $e) {
            Log::error('Erreur lors du dispatch IA', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de la génération : ' . $e->getMessage());
            $this->isGenerating = false;
            $this->dispatch('refresh');
        }
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    #[On('open-contrat-form')]
    public function openModal($prestationId, $generateWithAi = false): void
    {
        Gate::authorize('create-contrat');

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
    public function viewContrat(string $contratId): void
    {
        Gate::authorize('see-contrat');

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
    public function sendContrat(string $id): void
    {
        Gate::authorize('send-contrat');

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
        if (Contrat::where('prestation_id', $this->prestationId)
            ->where('status', 'pending')->exists())
        {
            $warning = 'Un autre contrat est en attente de signature pour cette prestation.';
            session()->flash('warning', $warning);
            return;
        }

        if (Contrat::where('prestation_id', $this->prestationId)
            ->where('status', 'signed')->exists())
        {
            $warning = 'Sachez qu\'il existe déjà un contrat signé pour cette prestation.';
            session()->flash('warning', $warning);
            return;
        }
    }

    #[On('edit-contrat')]
    public function editContrat(string $contratId): void
    {
        Gate::authorize('edit-contrat');

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
            if (Contrat::where('id', $this->contratId)->exists()) {
                $contrat = Contrat::findOrFail($this->contratId);
                $contrat->update([
                    'content' => $this->form['content'],
                    'status' => $this->form['status'],
                ]);
                Log::info('Contrat mis à jour', [
                    'contratId' => $this->contratId,
                    'adminId' => Auth::guard('admin')->user()->id,
                ]);
            } else {
                $contrat = Contrat::create([
                    'prestation_id' => $this->prestationId,
                    'content' => $this->form['content'],
                    'status' => $this->form['status'],
                ]);
                $this->contratId = $contrat->id;
                Log::info('Contrat créé', [
                    'contratId' => $this->contratId,
                    'adminId' => Auth::guard('admin')->user()->id,
                ]);
            }
            session()->flash('success', 'Contrat sauvegardé avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    public function saveContrat($content = null): void
    {
        Gate::any(['create-contrat' , 'edit-contrat']);
        $this->saveLogic($content);
        $this->closeModal();
    }

    public function saveAndSend(): void
    {
        Gate::check(['create-contrat','send-contrat']);

        $this->saveLogic();
        if (!session()->has('error')) {
            $this->showSendOptions = true;
        }
    }

    private function prepareSend(string $method): void
    {
        Gate::authorize('send-contrat');

        if (Contrat::where('prestation_id', $this->prestationId)->where('status', 'pending')->exists()) {
            $warning = 'Un autre contrat est en attente de signature pour cette prestation.';
            $this->dispatch('send-by-' . $method, contratId: $this->contratId, warning: $warning);
            Log::warning('Warning affiché : contrat en attente', ['prestationId' => $this->prestationId]);
            return;
        }

        if (Contrat::where('prestation_id', $this->prestationId)->where('status', 'signed')->exists()) {
            $warning = 'Sachez qu\'il existe déjà un contrat signé pour cette prestation.';
            $this->dispatch('send-by-' . $method, contratId: $this->contratId, warning: $warning);
            Log::warning('Warning déjà signé existant : contrat déjà signé', ['prestationId' => $this->prestationId]);
            return;
        }

        $this->dispatch('send-by-' . $method, contratId: $this->contratId);
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
        Gate::authorize('delete-contrat');

        $contrat = Contrat::findOrFail($this->contratId);
        if ($contrat->status != 'draft') {
            session()->flash('warning', 'Impossible de supprimer un contrat qui est sorti de votre brouillon.');
            return;
        }
        $contrat->delete();
        Log::info('Contrat supprimé', [
            'contratId' => $this->contratId,
            'adminId' => Auth::guard('admin')->user()->id,
        ]);
        $this->closeModal();

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
    }

    #[On('quill-content-updated')]
    public function updateContentFromQuill(string $content): void
    {
        Log::info('Mise à jour du contenu depuis Quill', [
            'contentLength' => strlen($content),
        ]);
        $this->form['content'] = $content;
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.calendar.contrat.contrat-form-modal');
    }
}

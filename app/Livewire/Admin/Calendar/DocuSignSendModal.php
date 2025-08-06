<?php

namespace App\Livewire\Admin\Calendar;

use Livewire\Component;
use App\Models\Contrat;
use App\Services\DocuSignService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;

class DocuSignSendModal extends Component
{
    public bool $showModal = false;
    public int|null $contratId = null;
    public string $method = '';
    public array $form = [
        'contractant_name' => '',
        'contractant_contact' => '',
        'artiste_name' => '',
        'artiste_contact' => '',
    ];
    public bool $errorOccurred = false;
    public string $errorMessage = '';

    public function mount(): void
    {
        $this->showModal = false;
    }

    #[On('send-by-email')]
    public function openModalEmail(int $contratId): void
    {
        $this->openModal($contratId, 'email');
    }

    #[On('send-by-sms')]
    public function openModalSMS(int $contratId): void
    {
        $this->openModal($contratId, 'sms');
    }

    #[On('send-by-whatsapp')]
    public function openModalWhatsApp(int $contratId): void
    {
        $this->openModal($contratId, 'whatsapp');
    }

    private function openModal(int $contratId, string $method): void
    {
        $this->contratId = $contratId;
        $this->method = $method;

        $contrat = Contrat::findOrFail($contratId);
        $prestation = $contrat->prestation;

        if (!$prestation) {
            $this->errorOccurred = true;
            $this->errorMessage = 'Prestation associée introuvable.';
            return;
        }

        $this->form['contractant_name'] = $prestation->nom_structure_contractante;
        $this->form['contractant_contact'] = $prestation->contact_organisateur;
        $this->form['artiste_name'] = $prestation->nom_representant_legal_artiste;
        $this->form['artiste_contact'] = $prestation->contact_artiste;

        $this->showModal = true;
        $this->resetErrorBag();
        $this->errorOccurred = false;
        $this->errorMessage = '';
    }

    public function sendByMail(): void
    {
        $this->validate([
            'form.contractant_contact' => 'required|email',
        ], [
            'form.contractant_contact.required' => 'L\'email du contractant est requis.',
            'form.contractant_contact.email' => 'L\'email doit être valide.',
        ]);

        $this->sendWithDocuSign('email');
    }

    public function sendByWhatsApp(): void
    {
        $this->validate([
            'form.contractant_contact' => 'required|regex:/^\+[1-9]\d{1,14}$/',
        ], [
            'form.contractant_contact.required' => 'Le numéro WhatsApp du contractant est requis.',
            'form.contractant_contact.regex' => 'Le numéro doit être au format international (ex. : +33612345678).',
        ]);

        $this->sendWithDocuSign('whatsapp');
    }

    public function sendBySMS(): void
    {
        $this->validate([
            'form.contractant_contact' => 'required|regex:/^\+[1-9]\d{1,14}$/',
        ], [
            'form.contractant_contact.required' => 'Le numéro SMS du contractant est requis.',
            'form.contractant_contact.regex' => 'Le numéro doit être au format international (ex. : +33612345678).',
        ]);

        $this->sendWithDocuSign('sms');
    }

    private function sendWithDocuSign(string $method): void
    {
        $contrat = Contrat::findOrFail($this->contratId);

        // Mettre à jour contact_organisateur si modifié dans le formulaire
        $prestation = $contrat->prestation;
        if ($prestation->contact_organisateur !== $this->form['contractant_contact']) {
            $prestation->contact_organisateur = $this->form['contractant_contact'];
            $prestation->save();
            Log::info('Mise à jour de contact_organisateur dans DocuSignSendModal', [
                'prestationId' => $prestation->id,
                'old_contact' => $prestation->contact_organisateur,
                'new_contact' => $this->form['contractant_contact'],
            ]);
        }

        $docusignService = new DocuSignService();
        $result = $docusignService->sendEnvelope($contrat, $method);

        if ($result['success']) {
            session()->flash('success', $result['message']);
            $this->closeModal();
        } else {
            $this->errorOccurred = true;
            $this->errorMessage = $result['error'];
        }
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $id = $this->contratId ;
        $this->reset(['contratId', 'method', 'form', 'errorOccurred', 'errorMessage']);
        $this->dispatch('send-contrat', id: $id);

    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.calendar.docu-sign-send-modal');
    }
}

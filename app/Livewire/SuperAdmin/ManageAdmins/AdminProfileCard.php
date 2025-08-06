<?php

namespace App\Livewire\SuperAdmin\ManageAdmins;

use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminProfileCard extends Component
{
    use WithFileUploads;

    public Admin|null $admin = null;

    public string $nom = '';
    public string $prenom = '';
    public string|null $email = '';
    public string $telephone = '';
    public ?string $pays = null;
    public ?string $ville = null;

    public $newPhotoProfil;
    public $newPieceIdentiteRecto;
    public $newPieceIdentiteVerso;

    public bool $deletePhotoProfil = false;
    public bool $deletePieceIdentiteRecto = false;
    public bool $deletePieceIdentiteVerso = false;

    public array $selectedRoles = [];
    public bool $showModal = false;
    public bool $showPieceIdentiteRecto = true;

    protected $listeners = ['openAdminProfileCard' => 'openModal'];

    public function getAvailableRolesProperty()
    {
        return Role::where('guard_name', 'admin')->orderBy('name')->get();
    }

    protected function rules(): array
    {
        $adminId = $this->admin ? $this->admin->id : null;

        return [
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('admins')->ignore($adminId)->where(fn ($query) => $query->whereNotNull('email')),
            ],
            'telephone' => ['nullable', 'string', 'max:20', Rule::unique('admins', 'telephone')->ignore($adminId)],
            'pays' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
            'newPhotoProfil' => ['nullable', 'image', 'max:1024'],
            'newPieceIdentiteRecto' => ['nullable', 'image', 'max:2048'],
            'newPieceIdentiteVerso' => ['nullable', 'image', 'max:2048'],
            'selectedRoles' => ['nullable', 'array'],
            'selectedRoles.*' => ['exists:roles,id'],
            'deletePhotoProfil' => ['boolean'],
            'deletePieceIdentiteRecto' => ['boolean'],
            'deletePieceIdentiteVerso' => ['boolean'],
        ];
    }

    protected array $messages = [
        'email.unique' => 'Cet e-mail est déjà utilisé par un autre administrateur.',
        'telephone.unique' => 'Ce numéro de téléphone est déjà utilisé par un autre administrateur.',
        'newPhotoProfil.max' => 'La photo de profil ne doit pas dépasser 1MB.',
    ];

    public function openModal(int $adminId): void
    {
        $this->admin = Admin::withTrashed()->find($adminId);

        if (!$this->admin) {
            session()->flash('error', "Administrateur non trouvé.");
            $this->closeModal();
            return;
        }

        $this->fill([
            'nom' => $this->admin->nom,
            'prenom' => $this->admin->prenom,
            'email' => $this->admin->email,
            'telephone' => $this->admin->telephone,
            'pays' => $this->admin->pays,
            'ville' => $this->admin->ville,
            'selectedRoles' => $this->admin->roles->pluck('id')->toArray(),
        ]);

        $this->reset([
            'newPhotoProfil', 'newPieceIdentiteRecto', 'newPieceIdentiteVerso',
            'deletePhotoProfil', 'deletePieceIdentiteRecto', 'deletePieceIdentiteVerso',
        ]);

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetValidation();
        $this->reset([
            'admin', 'nom', 'prenom', 'email', 'telephone', 'pays', 'ville',
            'newPhotoProfil', 'newPieceIdentiteRecto', 'newPieceIdentiteVerso',
            'deletePhotoProfil', 'deletePieceIdentiteRecto', 'deletePieceIdentiteVerso',
            'selectedRoles','showPieceIdentiteRecto'
        ]);
    }

    public function updateAdmin(): void
    {
        if (!$this->admin) {
            session()->flash('error', "Aucun administrateur sélectionné pour la mise à jour.");
            return;
        }

        $this->validate();

        try {
            $adminData = [
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => !empty($this->email) ? $this->email : null,
                'telephone' => $this->telephone,
                'pays' => $this->pays,
                'ville' => $this->ville,
            ];

            if ($this->deletePhotoProfil && $this->admin->photoProfil) {
                Storage::disk('public')->delete($this->admin->photoProfil);
                $adminData['photoProfil'] = null;
            }
            if ($this->deletePieceIdentiteRecto && $this->admin->pieceIdentiteRecto) {
                Storage::disk('public')->delete($this->admin->pieceIdentiteRecto);
                $adminData['pieceIdentiteRecto'] = null;
            }
            if ($this->deletePieceIdentiteVerso && $this->admin->pieceIdentiteVerso) {
                Storage::disk('public')->delete($this->admin->pieceIdentiteVerso);
                $adminData['pieceIdentiteVerso'] = null;
            }

            if ($this->newPhotoProfil) {
                if ($this->admin->photoProfil) {
                    Storage::disk('public')->delete($this->admin->photoProfil);
                }
                $adminData['photoProfil'] = $this->newPhotoProfil->store('admin_profiles', 'public');
            }
            if ($this->newPieceIdentiteRecto) {
                if ($this->admin->pieceIdentiteRecto) {
                    Storage::disk('public')->delete($this->admin->pieceIdentiteRecto);
                }
                $adminData['pieceIdentiteRecto'] = $this->newPieceIdentiteRecto->store('id_pieces', 'public');
            }
            if ($this->newPieceIdentiteVerso) {
                if ($this->admin->pieceIdentiteVerso) {
                    Storage::disk('public')->delete($this->admin->pieceIdentiteVerso);
                }
                $adminData['pieceIdentiteVerso'] = $this->newPieceIdentiteVerso->store('id_pieces', 'public');
            }

            $this->admin->update($adminData);

            if (!empty($this->selectedRoles)) {
                $roles = Role::whereIn('id', $this->selectedRoles)
                    ->where('guard_name', 'admin')
                    ->with('permissions')
                    ->get();
                $this->admin->syncRoles($roles);
            } else {
                $this->admin->syncRoles([]);
            }

            session()->flash('success', 'Profil de "' . $this->admin->prenom . ' ' . $this->admin->nom . '" mis à jour avec succès !');
            $this->dispatch('adminUpdated');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour du profil: ' . $e->getMessage());
            Log::error('AdminProfileCard - Erreur updateAdmin: ' . $e->getMessage(), ['exception' => $e]);
        }
    }


    public function generatePdf(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$this->admin) {
            session()->flash('error', "Impossible de générer le PDF. Aucun administrateur sélectionné.");
            $this->closeModal();
            abort(404, "Admin not found for PDF generation.");
        }

        $admin = Admin::with(['roles' => function($query) {
            $query->with('permissions');
        }])->find($this->admin->id);

        if (!$admin) {
            session()->flash('error', "Administrateur non trouvé pour la génération du PDF.");
            $this->closeModal();
            abort(404, "Admin not found for PDF generation.");
        }

        $photoProfilPath = null;
        if ($admin->photoProfil && Storage::disk('public')->exists($admin->photoProfil)) {
            $photoProfilPath = Storage::disk('public')->path($admin->photoProfil);
        }

        $pieceIdentiteRectoPath = null;
        if ($admin->pieceIdentiteRecto && Storage::disk('public')->exists($admin->pieceIdentiteRecto)) {
            $pieceIdentiteRectoPath = Storage::disk('public')->path($admin->pieceIdentiteRecto);
        }

        $pieceIdentiteVersoPath = null;
        if ($admin->pieceIdentiteVerso && Storage::disk('public')->exists($admin->pieceIdentiteVerso)) {
            $pieceIdentiteVersoPath = Storage::disk('public')->path($admin->pieceIdentiteVerso);
        }

        $qrCodeSvg = QrCode::size(150)->generate($admin->matricule)->toHtml();
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.admin_profile', [
            'admin' => $admin,
            'photoProfilPath' => $photoProfilPath,
            'pieceIdentiteRectoPath' => $pieceIdentiteRectoPath,
            'pieceIdentiteVersoPath' => $pieceIdentiteVersoPath,
            'qrCodeBase64' => $qrCodeBase64,
        ]);

        $pdf->setPaper('A4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'fiche_admin_' . $admin->nom.' '.$admin->prenom . '.pdf');
    }

    public function togglePieceIdentiteView(): void
    {
        $this->showPieceIdentiteRecto = !$this->showPieceIdentiteRecto;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.super-admin.manage-admins.admin-profile-card');
    }
}

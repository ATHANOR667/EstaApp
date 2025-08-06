<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileDisplay extends Component
{
    public ?Admin $admin = null;

    public ?string $nom = null;
    public ?string $prenom = null;
    public ?string $matricule = null;
    public ?string $email = null;
    public ?string $telephone = null;
    public ?string $pays = null;
    public ?string $ville = null;
    public ?string $photoProfilUrl = null;
    public ?string $pieceIdentiteRectoUrl = null;
    public ?string $pieceIdentiteVersoUrl = null;
    public ?string $passwordChangedAt = null;
    public ?string $passcodeResetDate = null;

    public bool $showPieceIdentiteRecto = true;


    public function mount(): void
    {
        $this->admin = Auth::guard('admin')->user();

        if ($this->admin) {
            $this->fill([
                'nom' => $this->admin->nom,
                'prenom' => $this->admin->prenom,
                'matricule' => $this->admin->matricule,
                'email' => $this->admin->email,
                'telephone' => $this->admin->telephone,
                'pays' => $this->admin->pays,
                'ville' => $this->admin->ville,
                'photoProfilUrl' => $this->admin->photoProfil ? asset('storage/' . $this->admin->photoProfil) : null,
                'pieceIdentiteRectoUrl' => $this->admin->pieceIdentiteRecto ? asset('storage/' . $this->admin->pieceIdentiteRecto) : null,
                'pieceIdentiteVersoUrl' => $this->admin->pieceIdentiteVerso ? asset('storage/' . $this->admin->pieceIdentiteVerso) : null,
                'passwordChangedAt' => $this->admin->password_changed_at ? $this->admin->password_changed_at->format('d/m/Y H:i') : 'Jamais',
                'passcodeResetDate' => $this->admin->passcode_reset_date ? $this->admin->passcode_reset_date->format('d/m/Y H:i') : 'Jamais',
            ]);
        }
    }


    public function togglePieceIdentite(): void
    {
        $this->showPieceIdentiteRecto = !$this->showPieceIdentiteRecto;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.profile-display');
    }
}

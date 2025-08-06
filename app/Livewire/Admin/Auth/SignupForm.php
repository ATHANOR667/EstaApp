<?php

namespace App\Livewire\Admin\Auth;

use App\Models\Admin;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class SignupForm extends Component
{
    public string $matricule = '';
    public string $email = '';
    public bool $matriculeVerified = false;
    public ?Admin $admin = null;

    public string $otp = '';
    public bool $otpSent = false;
    public string $otpCacheKey = '';

    public string $password = '';
    public string $password_confirmation = '';
    public bool $showPasswordFields = false;

    public int $currentStep = 1;

    protected $otpService;

    public function boot(OtpService $otpService): void
    {
        $this->otpService = $otpService;
    }

    protected function rules(): array
    {
        return [
            'matricule' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'string', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function updated($propertyName): void
    {
        if ($this->currentStep === 1) {
            $this->validateOnly($propertyName, [
                'matricule' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
            ]);
        } elseif ($this->currentStep === 2) {
            $this->validateOnly($propertyName, ['otp' => ['required', 'string', 'digits:6']]);
        } elseif ($this->currentStep === 3) {
            $this->validateOnly($propertyName, [
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'password_confirmation' => ['required'],
            ]);
        }
    }


    /**
     * @throws ValidationException
     */
    public function verifyMatriculeAndSendOtp(): void
    {
        $this->validate([
            'matricule' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $this->admin = Admin::where('matricule', $this->matricule)->first();

        if (!$this->admin) {
            throw ValidationException::withMessages([
                'matricule' => 'Ce matricule n\'existe pas.',
            ]);
        }

        if ($this->admin->password !== null) {
            throw ValidationException::withMessages([
                'matricule' => 'Cet administrateur est déjà inscrit. Veuillez vous connecter ou réinitialiser votre mot de passe.',
            ]);
        }

        if ($this->admin->email) {

            if ($this->admin->email !== $this->email) {
                throw ValidationException::withMessages([
                    'email' => 'L\'adresse email ne correspond pas au matricule fourni.',
                ]);
            }
        } else {

            if (Admin::withTrashed()->where('email', $this->email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'Un compte existe déjà pour cette adresse email.'
                ]);
            }
        }


        try {
            $this->otpCacheKey = 'admin_signup_' . $this->email;
            $otpData = [
                'matricule' => $this->matricule,
                'email' => $this->email,
                'admin_id' => $this->admin->id,
                'purpose' => 'admin_signup',
            ];

            $this->otpService->sendOtp($this->email, $this->otpCacheKey, $otpData);
            $this->otpSent = true;
            $this->currentStep = 2;
            session()->flash('success', 'Un code OTP a été envoyé à votre adresse email.');
        } catch (\Exception $e) {
            Log::error("Error sending OTP for admin signup: " . $e->getMessage(), ['matricule' => $this->matricule, 'email' => $this->email]);
            session()->flash('error', 'Impossible d\'envoyer le code OTP. ' . $e->getMessage());
        }
    }


    public function verifyOtp(): void
    {
        $this->validate(['otp' => ['required', 'string', 'digits:6']]);

        try {
            $verifiedData = $this->otpService->verifyOtp($this->otpCacheKey, $this->otp);

            if ($verifiedData) {
                $this->admin = Admin::find($verifiedData['admin_id']);
                if (!$this->admin) {
                    throw new \Exception("Admin not found after OTP verification.");
                }
                $this->showPasswordFields = true;
                $this->currentStep = 3;
                session()->flash('success', 'Code OTP vérifié. Vous pouvez maintenant définir votre mot de passe.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la vérification de l\'OTP: ' . $e->getMessage());
        }
    }


    public function registerAdmin()
    {
        $this->validate([
            'password' => ['required', 'string', 'min:8', 'same:password_confirmation'],
            'password_confirmation' => ['required'],
        ]);

        if (!$this->admin) {
            session()->flash('error', 'Une erreur est survenue. Veuillez recommencer le processus.');
            $this->resetForm();
        }

        try {
            $this->admin->update([
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            session()->flash('success', 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.');
            return redirect()->route('admin.auth.disconnected.loginView');
        } catch (\Exception $e) {
            Log::error("Error registering admin password: " . $e->getMessage(), ['admin_id' => $this->admin->id]);
            session()->flash('error', 'Une erreur est survenue lors de l\'enregistrement de votre mot de passe.');
        }
    }

    public function resendOtp(): void
    {
        if (!$this->admin) {
            session()->flash('error', 'Veuillez d\'abord entrer votre matricule et email.');
            $this->resetForm();
            return;
        }

        try {
            $this->otpService->sendOtp($this->email, $this->otpCacheKey, [
                'matricule' => $this->matricule,
                'email' => $this->email,
                'admin_id' => $this->admin->id,
                'purpose' => 'admin_signup',
            ]);
            session()->flash('success', 'Un nouveau code OTP a été envoyé à votre adresse email.');
        } catch (\Exception $e) {
            session()->flash('error', 'Impossible de renvoyer le code OTP. ' . $e->getMessage());
        }
    }

    public function resetForm(): void
    {
        $this->reset(['matricule', 'email', 'otp', 'password', 'password_confirmation', 'matriculeVerified', 'otpSent', 'showPasswordFields', 'admin', 'otpCacheKey']);
        $this->currentStep = 1;
        session()->forget(['success', 'error', 'message']);
    }


    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.admin.auth.signup-form');
    }
}

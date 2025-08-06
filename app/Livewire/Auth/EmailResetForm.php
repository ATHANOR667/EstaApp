<?php

namespace App\Livewire\Auth;

use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class EmailResetForm extends Component
{
    public int $step = 1;
    public string $newEmail = '';
    public string $currentPassword = '';
    public string $otp = '';

    public string $guard;

    public string $dbName ;

    #[on('dataUpdate')]
    public function actualize(): void
    {
        $this->mount($this->guard);
    }

    public function mount( string $guard ): void
    {
        $this->guard = $guard;
        $this->dbName = $guard == 'super-admin' ? 'super_admin' : $guard;
    }

    public function sendEmailOtp(): void
    {
        $this->validate([
            'newEmail' => ['required', 'string', 'email', 'max:255', 'unique:'.$this->dbName.'s,email'],
            'currentPassword' => ['required', 'string'],
        ], [
            'newEmail.unique' => "Cette adresse email est déjà utilisée.",
            'currentPassword.required' => "Votre mot de passe actuel est requis pour confirmer."
        ]);

        $user = Auth::guard($this->guard)->user();

        if (!Hash::check($this->currentPassword, $user->password)) {
            $this->addError('currentPassword', 'Le mot de passe actuel est incorrect.');
            return;
        }

        $cacheKey = 'otp_change_email_'.$this->guard.'_' . $user->id;
        $otpData = [
            'user_id' => $user->id,
            'purpose' => 'change_email',
            'new_email' => $this->newEmail,
        ];

        try {
            $otpService = new OtpService() ;
            $otpService->sendOtp($this->newEmail, $cacheKey, $otpData);
            $this->step = 2;
            session()->flash('email_reset_success', 'Un code OTP a été envoyé à votre NOUVELLE adresse email. Veuillez vérifier votre boîte de réception pour confirmer.');
        } catch (\Exception $e) {
            $this->addError('newEmail', $e->getMessage());
        }
    }

    public function processEmailChange(): void
    {
        $this->validate([
            'otp' => ['required', 'string', 'digits:6'],
        ], [
            'otp.required' => "Le code OTP est requis.",
            'otp.digits' => "Le code OTP doit contenir 6 chiffres.",
        ]);

        $user = Auth::guard($this->guard)->user();
        $cacheKey = 'otp_change_email_'.$this->guard.'_' . $user->id;

        try {
            $otpService = new OtpService() ;
            $otpData = $otpService->verifyOtp($cacheKey, $this->otp);

            if ($otpData === null || $otpData['purpose'] !== 'change_email' || $otpData['user_id'] !== $user->id || $otpData['new_email'] !== $this->newEmail) {
                throw ValidationException::withMessages(['otp' => 'Code OTP invalide ou ne correspond pas à l\'action demandée.']);
            }

            $user->email = $this->newEmail;
            $user->save();
            $this->reset(['step', 'newEmail', 'currentPassword', 'otp']);
            $this->dispatch('refreshComponent');
            $this->dispatch('dataUpdate');

            session()->flash('success', 'Votre email a été mis à jour avec succès !');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->addError('otp', $e->getMessage());
        }
    }

    public function goBackToStep1(): void
    {
        $this->step = 1;
        $this->otp = '';
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.auth.email-reset-form');
    }
}

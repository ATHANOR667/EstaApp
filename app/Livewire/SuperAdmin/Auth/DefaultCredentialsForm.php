<?php

namespace App\Livewire\SuperAdmin\Auth;

use App\Services\OtpService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class DefaultCredentialsForm extends Component
{
    public string $initialEmail = '';
    public string $initialOtp = '';
    public string $initialPassword = '';
    public string $initialPasswordConfirmation = '';
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

        $this->reset(['initialEmail', 'initialOtp', 'initialPassword', 'initialPasswordConfirmation']);
        $this->initialEmail = old('initial_email', '');

        if (session()->has('errors')) {
            if (session('errors')->has('initialEmail') || session('errors')->has('otp_send')) {
                $this->dispatch('set-default-credentials-step', step: 1);
            } elseif (session('errors')->has('initialOtp') || session('errors')->has('initialPassword') || session('errors')->has('initialPasswordConfirmation')) {
                $this->dispatch('set-default-credentials-step', step: 2);
            }
        } elseif (session()->has('success') && str_contains(session('success'), 'OTP')) {
            $this->dispatch('set-default-credentials-step', step: 2);
        } else {
            $this->dispatch('set-default-credentials-step', step: 1);
        }
    }

    public function sendOtp(): void
    {
        $this->validate([
            'initialEmail' => ['required', 'string', 'email', 'max:255', 'unique:'.$this->dbName.'s,email'],
        ], [
            'initialEmail.unique' => "Cette adresse email est déjà utilisée.",
        ]);

        $user = Auth::guard($this->guard)->user();
        $cacheKey = 'otp_default_erase_'.$this->guard.'_' . $user->id;
        $otpData = [
            'user_id' => $user->id,
            'purpose' => 'default_credentials_erase',
            'email' => $this->initialEmail,
        ];

        try {
            $otpService = new OtpService() ;
            $otpService->sendOtp($this->initialEmail, $cacheKey, $otpData);
            $this->dispatch('set-default-credentials-step', step: 2);
            session()->flash('success', 'Un code OTP a été envoyé à votre email. Veuillez vérifier votre boîte de réception.');
        } catch (\Exception $e) {
            $this->addError('initialEmail', $e->getMessage());
        }
    }

    public function processCredentials(): void
    {
        $this->validate([
            'initialOtp' => ['required', 'string', 'digits:6'],
            'initialPassword' => ['required', 'string', 'min:8', 'same:initialPasswordConfirmation'],
        ], [
            'initialOtp.required' => "Le code OTP est requis.",
            'initialOtp.digits' => "Le code OTP doit contenir 6 chiffres.",
            'initialPassword.min' => "Le mot de passe doit contenir au moins 8 caractères.",
            'initialPassword.same' => "La confirmation du mot de passe ne correspond pas.",
        ]);

        $user = Auth::guard($this->guard)->user();
        $cacheKey = 'otp_default_erase_'.$this->guard.'_' . $user->id;

        try {
            $otpService = new OtpService() ;
            $otpData = $otpService->verifyOtp($cacheKey, $this->initialOtp);

            if ($otpData === null || $otpData['purpose'] !== 'default_credentials_erase' || $otpData['user_id'] !== $user->id) {
                throw ValidationException::withMessages(['initialOtp' => 'Code OTP invalide ou ne correspond pas à l\'action demandée.']);
            }

            $user->email = $this->initialEmail;
            $user->password = Hash::make($this->initialPassword);
            $user->password_changed_at = now();
            $user->save();

            $this->reset(['initialEmail', 'initialOtp', 'initialPassword', 'initialPasswordConfirmation']);
            $this->dispatch('hide-default-credentials-form');
            $this->dispatch('set-default-credentials-step', step: 1);
            $this->dispatch('dataUpdate');

            session()->flash('success', 'Vos identifiants par défaut ont été mis à jour avec succès !');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->addError('initialOtp', $e->getMessage());
        }
    }

    public function goBackToStep1(): void
    {
        $this->initialOtp = '';
    }

    public function closeForm(): void
    {
        $this->reset(['initialEmail', 'initialOtp', 'initialPassword', 'initialPasswordConfirmation']);
        $this->dispatch('hide-default-credentials-form');
        $this->dispatch('set-default-credentials-step', step: 1);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.super-admin.auth.default-credentials-form');
    }
}

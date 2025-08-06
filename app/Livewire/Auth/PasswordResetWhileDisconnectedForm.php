<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordResetWhileDisconnectedForm extends Component
{

    public string $email = '';
    public string $otp = '';
    public string $password = '';
    public string $passwordConfirmation = '';
    public string $guard;
    public string $table ;


    public function mount( string $guard ): void
    {
        $this->guard = $guard;

        $userModel = config('auth.guards.' . $this->guard . '.provider');
        $userClass = config('auth.providers.' . $userModel . '.model');

        $userInstance = new $userClass;

        $this->table = $userInstance->getTable();

        $this->reset(['email', 'otp', 'password', 'passwordConfirmation']);

        $this->dispatch('set-password-reset-step', step: 1);
    }

    public function sendOtp(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:'.$this->table.',email'],
        ], [
            'email.exists' => "Cette adresse email n'est pas enregistrée dans notre système.",
        ]);


        $userModel = config('auth.guards.' . $this->guard . '.provider');
        $userClass = config('auth.providers.' . $userModel . '.model');

        $userInstance = new $userClass;

        $tableName = $userInstance->getTable();

        $user = $userClass::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', "Impossible de trouver un utilisateur avec cette adresse email.");
            return;
        }

        $cacheKey = 'otp_password_reset_'.$this->guard.'_' . $user->id;
        $otpData = [
            'user_id' => $user->id,
            'purpose' => 'password_reset',
            'email' => $this->email,
        ];

        try {
            $otpService = new OtpService() ;
            $otpService->sendOtp($this->email, $cacheKey, $otpData);
            $this->dispatch('set-password-reset-step', step: 2);
            session()->flash('password_reset_success', 'Un code OTP a été envoyé à votre email. Veuillez vérifier votre boîte de réception.');
        } catch (\Exception $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function resetPassword(): void
    {
        $this->validate([
            'otp' => ['required', 'string', 'digits:6'],
            'password' => ['required', 'string', 'min:8', 'same:passwordConfirmation'],
            'passwordConfirmation' => ['required'],
        ], [
            'otp.required' => "Le code OTP est requis.",
            'otp.digits' => "Le code OTP doit contenir 6 chiffres.",
            'password.min' => "Le mot de passe doit contenir au moins 8 caractères.",
            'password.same' => "La confirmation du mot de passe ne correspond pas.",
            'passwordConfirmation.required' => "Veuillez confirmer votre nouveau mot de passe.",
        ]);

        $userModel = config('auth.guards.'.$this->guard.'.provider');
        $userClass = config('auth.providers.'.$userModel.'.model');
        $user = $userClass::where('email', $this->email)->first();

        if (!$user) {
            throw ValidationException::withMessages(['email' => "Utilisateur introuvable pour la réinitialisation du mot de passe."]);
        }

        $cacheKey = 'otp_password_reset_'.$this->guard.'_' . $user->id;

        try {
            $otpService = new OtpService() ;
            $otpData = $otpService->verifyOtp($cacheKey, $this->otp);

            if ($otpData === null || $otpData['purpose'] !== 'password_reset' || $otpData['user_id'] !== $user->id || $otpData['email'] !== $this->email) {
                throw ValidationException::withMessages(['otp' => 'Code OTP invalide ou ne correspond pas à la demande.']);
            }

            $user->password = Hash::make($this->password);
            $user->save();

            $this->reset(['email', 'otp', 'password', 'passwordConfirmation']);
            $this->dispatch('set-password-reset-step', step: 1);
            $this->dispatch('show-login-form');

            session()->flash('success', 'Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->addError('otp', $e->getMessage());
        }
    }

    public function goBackToStep1(): void
    {
        $this->reset(['otp', 'password', 'passwordConfirmation']);
        $this->dispatch('set-password-reset-step', step: 1);
    }

    public function cancelReset(): void
    {
        $this->reset(['email', 'otp', 'password', 'passwordConfirmation']);
        $this->dispatch('set-password-reset-step', step: 1);
        $this->dispatch('show-login-form');
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.auth.password-reset-while-disconnected-form');
    }
}

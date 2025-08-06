<?php

namespace App\Livewire\Auth;

use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class PasswordResetForm extends Component
{
    public int $step = 1;
    public string $otp = '';
    public string $newPassword = '';
    public string $newPasswordConfirmation = '';

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

    public function sendPasswordOtp(): void
    {
        $user = Auth::guard($this->guard)->user();

        if (is_null($user->email)) {
            $this->addError('otp', 'Vous devez d\'abord configurer votre email par défaut avant de changer votre mot de passe.');
            return;
        }

        $cacheKey = 'otp_change_password_'.$this->guard.'_' . $user->id;
        $otpData = [
            'user_id' => $user->id,
            'purpose' => 'change_password',
        ];

        try {
            $otpService = new OtpService() ;
            $otpService->sendOtp($user->email, $cacheKey, $otpData);
            $this->step = 2;
            session()->flash('password_reset_success', 'Un code OTP a été envoyé à votre email enregistré. Veuillez vérifier votre boîte de réception.');
        } catch (\Exception $e) {
            $this->addError('otp', $e->getMessage());
        }
    }

    public function processPasswordChange(): void
    {
        $this->validate([
            'otp' => ['required', 'string', 'digits:6'],
            'newPassword' => ['required', 'string', 'min:8', 'same:newPasswordConfirmation'],
        ], [
            'otp.required' => "Le code OTP est requis.",
            'otp.digits' => "Le code OTP doit contenir 6 chiffres.",
            'newPassword.min' => "Le mot de passe doit contenir au moins 8 caractères.",
            'newPassword.same' => "La confirmation du mot de passe ne correspond pas.",
        ]);

        $user = Auth::guard($this->guard)->user();
        $cacheKey = 'otp_change_password_'.$this->guard.'_' . $user->id;

        try {
            $otpService = new OtpService() ;
            $otpData = $otpService->verifyOtp($cacheKey, $this->otp);

            if ($otpData === null || $otpData['purpose'] !== 'change_password' || $otpData['user_id'] !== $user->id) {
                throw ValidationException::withMessages(['otp' => 'Code OTP invalide ou ne correspond pas à l\'action demandée.']);
            }

            $user->password = Hash::make($this->newPassword);
            $user->password_changed_at = now();
            $user->save();

            $this->reset(['step', 'otp', 'newPassword', 'newPasswordConfirmation']);
            $this->dispatch('dataUpdate');

            session()->flash('success', 'Votre mot de passe a été mis à jour avec succès !');
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
        return view('livewire.auth.password-reset-form');
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OtpMail;

class OtpService
{
    protected int $otpExpiryMinutes = 5;
    protected int $otpResendCooldownMinutes = 1;

    /**
     * Sends an OTP to the given email address.
     *
     * @param string|null $recipientEmail The email address to send the OTP to.
     * @param string $cacheKey The unique key to store the OTP in cache (e.g., 'otp_purpose_user_id').
     * @param array $dataToStoreWithOtp Additional data to store with the OTP (e.g., user_id, purpose).
     * @throws \Exception If the recipient email is invalid or OTP cannot be sent.
     * @throws \Exception If an OTP has been sent recently for this key.
     */
    public function sendOtp(?string $recipientEmail, string $cacheKey, array $dataToStoreWithOtp): void
    {
        if (empty($recipientEmail)) {
            throw new \Exception("L'adresse email du destinataire n'est pas définie. Veuillez d'abord configurer votre email.");
        }

        if (Cache::has($cacheKey) && Cache::get($cacheKey)['sent_at']->diffInMinutes(now()) < $this->otpResendCooldownMinutes) {
            throw new \Exception("Un code OTP a déjà été envoyé récemment. Veuillez attendre avant d'en demander un nouveau.");
        }

        $otp = Str::padLeft(random_int(0, 999999), 6, '0');

        Cache::put($cacheKey, [
            'otp' => $otp,
            'sent_at' => now(),
            'data' => $dataToStoreWithOtp
        ], now()->addMinutes($this->otpExpiryMinutes));

        try {
            Mail::to($recipientEmail)->queue(new OtpMail($otp, $this->otpExpiryMinutes));
        } catch (\Exception $e) {
            Cache::forget($cacheKey);
            throw new \Exception("Impossible d'envoyer le code OTP à l'email fourni. Erreur: " . $e->getMessage());
        }
    }

    /**
     * Verifies the given OTP against the one stored in cache.
     *
     * @param string $cacheKey The unique key used to store the OTP in cache.
     * @param string $providedOtp The OTP provided by the user.
     * @return array|null The data stored with the OTP if successful, null otherwise.
     * @throws \Exception If the OTP is invalid or expired.
     */
    public function verifyOtp(string $cacheKey, string $providedOtp): ?array
    {
        if (!Cache::has($cacheKey)) {
            throw new \Exception("Code OTP invalide ou expiré.");
        }

        $cachedOtpData = Cache::get($cacheKey);

        if ($cachedOtpData['otp'] !== $providedOtp) {
            Cache::forget($cacheKey);
            throw new \Exception("Code OTP incorrect.");
        }

        Cache::forget($cacheKey);

        return $cachedOtpData['data'];
    }

    /**
     * Get the configured OTP expiry minutes.
     *
     * @return int
     */
    public function getOtpExpiryMinutes(): int
    {
        return $this->otpExpiryMinutes;
    }
}

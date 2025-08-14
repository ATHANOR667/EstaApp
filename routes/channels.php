<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

Broadcast::channel('contrat-form-modal.{userId}', function ($user, $userId) {
    $admin = Auth::guard('admin')->user();
    if (!$admin) {
        Log::error('Aucun utilisateur authentifié pour le guard admin', ['userId' => $userId]);
        return false;
    }
    $isAuthorized = (int) $admin->id === (int) $userId;
    Log::info('Autorisation du canal', ['userId' => $userId, 'adminId' => $admin->id, 'authorized' => $isAuthorized]);
    return $isAuthorized;
});

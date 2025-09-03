<?php

namespace App\Http\Controllers\ContratWebhookController;

use App\Jobs\ProcessZapSignWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ZapSignWebHookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            // Step 1: Validate ZapSign request
            if (!$this->verifyZapSignRequest($request)) {
                Log::warning('Invalid ZapSign webhook request', [
                    'ip' => $request->ip(),
                    'headers' => $request->headers->all(),
                ]);
                return response()->json(['status' => 'ignored', 'message' => 'Invalid request'], 400);
            }

            // Step 2: Parse JSON payload
            try {
                $data = json_decode($request->getContent(), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON: ' . json_last_error_msg());
                }

                $documentKey = $data['document']['document_key'] ?? null;
                $status = strtolower($data['document']['status'] ?? $data['event'] ?? '');

            } catch (\Exception $e) {
                Log::error('Failed to parse ZapSign webhook JSON', [
                    'content' => $request->getContent(),
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid JSON payload'], 400);
            }

            if (empty($documentKey) || empty($status)) {
                Log::error('Missing required fields in ZapSign webhook', [
                    'document_key' => $documentKey,
                    'status' => $status,
                ]);
                return response()->json(['status' => 'error', 'message' => 'Missing document key or status'], 400);
            }

            // Step 3: Log incoming webhook
            Log::info('ZapSign webhook received', [
                'document_key' => $documentKey,
                'status' => $status,
            ]);

            // Step 4: Dispatch to queue
            ProcessZapSignWebhook::dispatch([
                'document_key' => $documentKey,
                'status' => $status,
            ]);

            // Step 5: Immediate response to ZapSign
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Error handling ZapSign webhook', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    private function verifyZapSignRequest(Request $request): bool
    {
        $secret = config('services.zapsign.webhook_secret');
        if (empty($secret)) {
            Log::warning('Aucune clé secrète ZapSign configurée, HMAC non vérifié (mode dev)', [
                'ip' => $request->ip(),
            ]);
            return true; // bypass en dev
        }

        $signature = $request->header('x-zapsign-signature');
        if (!$signature) {
            Log::warning('En-tête x-zapsign-signature manquant', [
                'ip' => $request->ip(),
            ]);
            return false;
        }

        $payload = $request->getContent();
        if (empty($payload)) {
            Log::warning('Payload ZapSign vide', [
                'ip' => $request->ip(),
            ]);
            return false;
        }

        $calculatedSignature = hash_hmac('sha256', $payload, $secret);

        if (hash_equals($signature, $calculatedSignature)) {
            Log::info('Vérification HMAC ZapSign réussie', [
                'ip' => $request->ip(),
            ]);
            return true;
        }

        Log::warning('Échec de la vérification HMAC ZapSign', [
            'ip' => $request->ip(),
            'received_signature' => $signature,
        ]);

        return false;
    }
}

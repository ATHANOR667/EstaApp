<?php

namespace App\Http\Controllers\ContratWebhookController;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessDocuSignWebhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocuSignWebhookController extends Controller
{
    public function handle(Request $request)
    {
        try {
            // Step 1: Validate DocuSign request
            if (!$this->verifyDocuSignRequest($request)) {
                Log::warning('Invalid DocuSign webhook request', [
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
                $envelopeId = $data['data']['envelopeId'] ?? null;
                $status = strtolower($data['event'] ?? '');
            } catch (\Exception $e) {
                Log::error('Failed to parse DocuSign webhook JSON', [
                    'content' => $request->getContent(),
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid JSON payload'], 400);
            }

            if (empty($envelopeId) || empty($status)) {
                Log::error('Missing required fields in DocuSign webhook', [
                    'envelope_id' => $envelopeId,
                    'status' => $status,
                ]);
                return response()->json(['status' => 'error', 'message' => 'Missing envelope ID or status'], 400);
            }

            // Step 3: Log incoming webhook
            Log::info('DocuSign webhook received', [
                'envelope_id' => $envelopeId,
                'status' => $status,
            ]);

            // Step 4: Dispatch to queue
            ProcessDocuSignWebhook::dispatch([
                'envelope_id' => $envelopeId,
                'status' => $status,
            ]);

            // Step 5: Immediate response to DocuSign
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('Error handling DocuSign webhook', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    private function verifyDocuSignRequest(Request $request): bool
    {
        $secrets = (array) config('services.docusign.webhook_secret');
        if (empty($secrets)) {
            Log::error('Aucune clé secrète DocuSign configurée pour la vérification HMAC', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);
            return false;
        }

        $signature = $request->header('x-docusign-signature-1');
        if (!$signature) {
            Log::warning('En-tête x-docusign-signature-1 manquant', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);
            return false;
        }

        $payload = $request->getContent();
        if (empty($payload)) {
            Log::warning('Payload de la requête DocuSign vide', [
                'ip' => $request->ip(),
                'headers' => $request->headers->all(),
            ]);
            return false;
        }

        foreach ($secrets as $secret) {
            $calculatedSignature = base64_encode(hash_hmac('sha256', $payload, $secret, true));
            if (hash_equals($signature, $calculatedSignature)) {
                Log::info('Vérification HMAC réussie', [
                    'envelope_id' => $data['data']['envelopeId'] ?? 'unknown',
                    'ip' => $request->ip(),
                ]);
                return true;
            }
        }

        Log::warning('Échec de la vérification HMAC DocuSign', [
            'ip' => $request->ip(),
            'received_signature' => $signature,
            'envelope_id' => $data['data']['envelopeId'] ?? 'unknown',
        ]);
        return false;
    }
}

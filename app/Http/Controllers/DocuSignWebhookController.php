<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocuSignWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        Log::info('Webhook DocuSign reçu', ['data' => $data]);

        $event = $data['data']['envelopeSummary']['status'] ?? null;

        switch ($event) {
            case 'sent':
                $this->handleEnvelopeSent($data);
                break;
            case 'completed':
                $this->handleEnvelopeCompleted($data);
                break;
            case 'declined':
                $this->handleEnvelopeDeclined($data);
                break;
            default:
                Log::warning('Événement DocuSign non géré', ['event' => $event]);
        }

        return response()->json(['status' => 'received']);
    }

    private function handleEnvelopeSent($data)
    {
        //todo: Mettre à jour le statut du contrat à 'pending' et enregistrer docusign_envelope_id
    }

    private function handleEnvelopeCompleted($data)
    {
        //todo: Mettre à jour le statut du contrat à 'signed' et récupérer le PDF signé
    }

    private function handleEnvelopeDeclined($data)
    {
        //todo: Mettre à jour le statut du contrat à 'rejected'
    }
}

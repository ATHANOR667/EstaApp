<?php

namespace App\Services;

use App\Models\Contrat;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\Tabs;
use DocuSign\eSign\Model\SignHere;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocuSignService
{
    public function sendEnvelope(Contrat $contrat, string $method): array
    {
        try {
            // Vérifier les contrats en attente
            if (Contrat::where('prestation_id', $contrat->prestation_id)->where('status', 'pending')->exists()) {
                return [
                    'success' => false,
                    'error' => 'Un autre contrat est en attente de signature pour cette prestation.',
                ];
            }

            // Récupérer la prestation associée
            $prestation = $contrat->prestation;

            // Vérifier que la prestation existe
            if (!$prestation) {
                Log::error('Prestation introuvable pour le contrat', ['contratId' => $contrat->id]);
                return [
                    'success' => false,
                    'error' => 'Prestation associée introuvable.',
                ];
            }

            // Récupérer les informations des signataires
            $contractant_contact = $prestation->contact_organisateur;
            $contractant_name = $prestation->nom_structure_contractante;
            $artiste_contact = $prestation->contact_artiste;
            $artiste_name = $prestation->nom_representant_legal_artiste;

            // Générer le PDF
            $pdf = $this->generatePdf($contrat);

            // Configurer l'API DocuSign
            $apiClient = new ApiClient();
            $apiClient->getOAuth()->setOAuthBasePath(config('docusign.oauth_base_path'));
            $accessToken = $apiClient->requestJWTUserToken(
                config('docusign.client_id'),
                config('docusign.user_id'),
                config('docusign.key_path'),
                config('docusign.scope')
            )[0]['access_token'];
            $apiClient->getConfig()->setAccessToken($accessToken);

            // Créer le document
            $document = new Document([
                'document_base64' => base64_encode($pdf),
                'name' => 'Contrat_' . $contrat->id . '.pdf',
                'file_extension' => 'pdf',
                'document_id' => '1',
            ]);

            // Configurer les signataires
            $artisteSigner = new Signer([
                'email' => $artiste_contact,
                'name' => $artiste_name,
                'recipient_id' => '1',
                'routing_order' => '1',
            ]);
            $artisteSignTab = new SignHere([
                'anchor_string' => '/signature-artiste/',
                'anchor_units' => 'pixels',
                'anchor_x_offset' => '0',
                'anchor_y_offset' => '0',
                'document_id' => '1',
                'page_number' => '1',
                'recipient_id' => '1',
            ]);
            $artisteSigner->setTabs(new Tabs(['sign_here_tabs' => [$artisteSignTab]]));

            $contractantSigner = new Signer([
                'email' => $method === 'email' ? $contractant_contact : null,
                'phone_number' => in_array($method, ['sms', 'whatsapp']) ? $contractant_contact : null,
                'name' => $contractant_name,
                'recipient_id' => '2',
                'routing_order' => '1',
            ]);
            if ($method === 'whatsapp') {
                $contractantSigner->setDeliveryMethod('WhatsApp');
            } elseif ($method === 'sms') {
                $contractantSigner->setDeliveryMethod('SMS');
            }
            $contractantSignTab = new SignHere([
                'anchor_string' => '/signature-contractant/',
                'anchor_units' => 'pixels',
                'anchor_x_offset' => '0',
                'anchor_y_offset' => '0',
                'document_id' => '1',
                'page_number' => '1',
                'recipient_id' => '2',
            ]);
            $contractantSigner->setTabs(new Tabs(['sign_here_tabs' => [$contractantSignTab]]));

            // Créer l'enveloppe
            $envelopeDefinition = new EnvelopeDefinition([
                'email_subject' => 'Signature du contrat #' . $contrat->id,
                'documents' => [$document],
                'recipients' => new \DocuSign\eSign\Model\Recipients([
                    'signers' => [$artisteSigner, $contractantSigner],
                ]),
                'status' => 'sent',
            ]);

            // Envoyer l'enveloppe
            $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($apiClient);
            $envelope = $envelopeApi->createEnvelope(config('docusign.account_id'), $envelopeDefinition);

            // Mettre à jour le contrat uniquement après un envoi réussi
            $contrat->signature_artiste_representant = true;
            $contrat->signature_contractant = true;
            $contrat->status = 'pending';
            $contrat->docusign_envelope_id = $envelope->getEnvelopeId();
            $contrat->save();

            // Mettre à jour contact_organisateur si modifié
            if ($prestation->contact_organisateur !== $contractant_contact) {
                Log::info('Mise à jour de contact_organisateur', [
                    'prestationId' => $prestation->id,
                    'old_contact' => $prestation->contact_organisateur,
                    'new_contact' => $contractant_contact,
                ]);
                $prestation->contact_organisateur = $contractant_contact;
                $prestation->save();
            }

            return [
                'success' => true,
                'message' => 'Contrat envoyé avec succès via ' . ucfirst($method) . ' !',
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi DocuSign', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => 'Erreur lors de l\'envoi : ' . $e->getMessage(),
            ];
        }
    }

    private function generatePdf(Contrat $contrat): string
    {
        $qrCodeUrl = URL::temporarySignedRoute(
            'contrats.download_pdf',
            now()->addDays(7),
            ['contrat' => $contrat->id]
        );
        $qrCodeSvg = QrCode::size(150)->generate($qrCodeUrl)->toHtml();
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.view_contract', [
            'contrat' => $contrat,
            'qrCodeSvg' => $qrCodeBase64,
            'dateEmission' => now()->format('d/m/Y'),
        ])->setPaper('A4', 'portrait');

        return $pdf->output();
    }
}

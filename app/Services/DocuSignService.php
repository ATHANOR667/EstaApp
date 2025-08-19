<?php

namespace App\Services;

use App\Models\Contrat;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Client\ApiException;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\EnvelopeDefinition;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\Tabs;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\DateSigned;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use Smalot\PdfParser\Parser;

class DocuSignService
{
    public function sendEnvelope(Contrat $contrat, string $method): array
    {
        try {
            $requiredConfigs = [
                'services.docusign.client_id' => 'client_id',
                'services.docusign.user_id' => 'user_id',
                'services.docusign.key_path' => 'key_path',
                'services.docusign.oauth_base_path' => 'oauth_base_path',
                'services.docusign.account_id' => 'account_id',
                'services.docusign.scope' => 'scope',
            ];
            foreach ($requiredConfigs as $key => $name) {
                if (empty(config($key))) {
                    Log::error("Configuration DocuSign manquante : {$name}");
                    return ['success' => false, 'error' => "Configuration DocuSign invalide : {$name} manquant."];
                }
            }

            // Récupérer la prestation associée
            $prestation = $contrat->prestation;
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

            // Validation des ancres dans le PDF
            if (!$this->validateAnchors($pdf)) {
                Log::error('Ancre(s) de signature ou date manquante(s) dans le PDF', ['contratId' => $contrat->id]);
                return [
                    'success' => false,
                    'error' => 'Ancre(s) de signature ou date manquante(s) dans le document PDF.',
                ];
            }

            // Configurer l'API DocuSign
            $apiClient = new ApiClient();
            $apiClient->getConfig()->setHost('https://demo.docusign.net/restapi');

            // Validation du fichier clé privée
            $keyPath = base_path(config('services.docusign.key_path'));
            if (!file_exists($keyPath) || !is_readable($keyPath)) {
                Log::error('Clé privée introuvable ou illisible', ['path' => $keyPath]);
                return ['success' => false, 'error' => 'Clé privée DocuSign inaccessible.'];
            }
            $keyContent = file_get_contents($keyPath);
            if ($keyContent === false) {
                Log::error('Échec de la lecture du fichier de clé privée', ['path' => $keyPath]);
                return ['success' => false, 'error' => 'Impossible de lire la clé privée DocuSign.'];
            }

            $apiClient->getOAuth()->setOAuthBasePath(config('services.docusign.oauth_base_path'));
            $tokenResponse = $apiClient->requestJWTUserToken(
                config('services.docusign.client_id'),
                config('services.docusign.user_id'),
                $keyContent,
                config('services.docusign.scope')
            );
            $accessToken = $tokenResponse[0]['access_token'];
            $apiClient->getConfig()->setAccessToken($accessToken);

            // Créer le document
            $document = new Document([
                'document_base64' => base64_encode($pdf),
                'name' => 'Contrat_' . $contrat->id . '.pdf',
                'file_extension' => 'pdf',
                'document_id' => '1',
            ]);

            if (config('app.debug')) {
                Log::debug('Artiste Contact: ' . $artiste_contact);
                Log::debug('Contractant Contact: ' . $contractant_contact);
                Log::debug('Method: ' . $method);
            }

            // Configurer les signataires avec ancres dynamiques
            $artisteAnchor = config('services.docusign.signature_artiste_anchor', '/signature-artiste/');
            $contractantAnchor = config('services.docusign.signature_contractant_anchor', '/signature-contractant/');
            $artisteDateAnchor = config('services.docusign.date_artiste_anchor', '/date-artiste/');
            $contractantDateAnchor = config('services.docusign.date_contractant_anchor', '/date-contractant/');

            $artisteSigner = new Signer([
                'email' => $artiste_contact,
                'name' => $artiste_name,
                'recipient_id' => '1',
                'routing_order' => '1',
            ]);
            $artisteSignTab = new SignHere([
                'anchor_string' => $artisteAnchor,
                'anchor_units' => 'pixels',
                'anchor_x_offset' => '0',
                'anchor_y_offset' => '0',
                'document_id' => '1',
                'page_number' => '1',
                'recipient_id' => '1',
            ]);
            $artisteDateTab = new DateSigned([
                'anchor_string' => $artisteDateAnchor,
                'anchor_units' => 'pixels',
                'anchor_x_offset' => '0',
                'anchor_y_offset' => '20',
                'document_id' => '1',
                'page_number' => '1',
                'recipient_id' => '1',
            ]);
            $artisteSigner->setTabs(new Tabs([
                'sign_here_tabs' => [$artisteSignTab],
                'date_signed_tabs' => [$artisteDateTab],
            ]));

            if (in_array($method, ['sms', 'whatsapp'])) {
                $phoneUtil = PhoneNumberUtil::getInstance();
                try {
                    $parsedNumber = $phoneUtil->parse($contractant_contact, null);
                    if (!$phoneUtil->isValidNumber($parsedNumber)) {
                        Log::error('Numéro de téléphone invalide pour ' . $method, ['contractant_contact' => $contractant_contact]);
                        return [
                            'success' => false,
                            'error' => 'Numéro de téléphone invalide pour ' . ucfirst($method) . '. Format attendu : +33XXXXXXXXX',
                        ];
                    }
                    $phoneNumber = $phoneUtil->format($parsedNumber, \libphonenumber\PhoneNumberFormat::E164);
                    $countryCode = '+' . $parsedNumber->getCountryCode();
                    $number = substr($phoneNumber, strlen($countryCode));
                } catch (NumberParseException $e) {
                    Log::error('Erreur de validation du numéro de téléphone', [
                        'contractant_contact' => $contractant_contact,
                        'error' => $e->getMessage(),
                    ]);
                    return [
                        'success' => false,
                        'error' => 'Erreur de validation du numéro de téléphone pour ' . ucfirst($method) . '.',
                    ];
                }
            }

            $contractantSigner = new Signer([
                'email' => $method === 'email' ? $contractant_contact : null,
                'phone_number' => in_array($method, ['sms', 'whatsapp']) ? [
                    'countryCode' => $countryCode,
                    'number' => $number
                ] : null,
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
                'anchor_string' => $contractantAnchor,
                'anchor_units' => 'pixels',
                'anchor_x_offset' => '0',
                'anchor_y_offset' => '0',
                'document_id' => '1',
                'page_number' => '1',
                'recipient_id' => '2',
            ]);
            $contractantDateTab = new DateSigned([
                'anchor_string' => $contractantDateAnchor,
                'anchor_units' => 'pixels',
                'anchor_x_offset' => '0',
                'anchor_y_offset' => '20',
                'document_id' => '1',
                'page_number' => '1',
                'recipient_id' => '2',
            ]);
            $contractantSigner->setTabs(new Tabs([
                'sign_here_tabs' => [$contractantSignTab],
                'date_signed_tabs' => [$contractantDateTab],
            ]));

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
            $envelope = $envelopeApi->createEnvelope(config('services.docusign.account_id'), $envelopeDefinition);

            $contrat->status = 'pending';
            $contrat->docusign_envelope_id = $envelope->getEnvelopeId();
            $contrat->docusign_document_id = $document->getDocumentId();
            $contrat->save();

            if ($prestation->contact_organisateur !== $contractant_contact) {
                if (config('app.debug')) {
                    Log::info('Mise à jour de contact_organisateur', [
                        'prestationId' => $prestation->id,
                        'old_contact' => $prestation->contact_organisateur,
                        'new_contact' => $contractant_contact,
                    ]);
                }
                $prestation->contact_organisateur = $contractant_contact;
                $prestation->save();
            }

            // Tampon lu et approuvé côté artiste
            $contrat->signature_artiste_representant = true;
            $contrat->save();

            return [
                'success' => true,
                'message' => 'Contrat envoyé avec succès via ' . ucfirst($method) . ' !',
            ];
        } catch (ApiException $e) {
            $errorMessage = 'Erreur lors de l\'envoi du contrat.';
            if ($e->getCode() === 401) {
                $errorMessage = 'Échec de l\'authentification DocuSign.';
            } elseif ($e->getCode() === 400) {
                $errorMessage = 'Requête invalide envoyée à DocuSign.';
            } elseif ($e->getCode() === 429) {
                $errorMessage = 'Limite de requêtes DocuSign dépassée.';
            }
            Log::error('Erreur DocuSign détaillée', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $e->getResponseBody(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => $errorMessage,
                'response' => $e->getResponseBody(),
            ];
        } catch (\Exception $e) {
            Log::error('Erreur DocuSign détaillée', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => 'Erreur inattendue lors de l\'envoi : ' . $e->getMessage(),
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

        $contrat->signature_artiste_representant = true;
        $contrat->signature_contractant = true;

        $pdf = Pdf::loadView('pdf.view_contract', [
            'contrat' => $contrat,
            'qrCodeSvg' => $qrCodeBase64,
            'dateEmission' => now()->format('d/m/Y'),
        ])->setPaper('A4', 'portrait');

        return $pdf->output();
    }

    private function validateAnchors(string $pdfContent): bool
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseContent($pdfContent);
            $text = $pdf->getText();

            $artisteAnchor = config('services.docusign.signature_artiste_anchor', '/signature-artiste/');
            $contractantAnchor = config('services.docusign.signature_contractant_anchor', '/signature-contractant/');
            $artisteDateAnchor = config('services.docusign.date_artiste_anchor', '/date-artiste/');
            $contractantDateAnchor = config('services.docusign.date_contractant_anchor', '/date-contractant/');

            $anchors = [
                $artisteAnchor => 'signature artiste',
                $contractantAnchor => 'signature contractant',
                $artisteDateAnchor => 'date artiste',
                $contractantDateAnchor => 'date contractant',
            ];

            foreach ($anchors as $anchor => $label) {
                $count = substr_count($text, $anchor);
                if ($count !== 1) {
                    Log::error("Validation de l'ancre échouée", [
                        'anchor' => $anchor,
                        'label' => $label,
                        'count' => $count,
                    ]);
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la validation des ancres dans le PDF', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return false;
        }
    }
}

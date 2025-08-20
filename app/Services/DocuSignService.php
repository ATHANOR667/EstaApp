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
    private ApiClient $apiClient;

    public function __construct()
    {
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
                throw new \RuntimeException("Configuration DocuSign invalide : {$name} manquant.");
            }
        }

        $this->apiClient = new ApiClient();
        $this->apiClient->getConfig()->setHost('https://demo.docusign.net/restapi');

        $keyPath = base_path(config('services.docusign.key_path'));
        if (!file_exists($keyPath) || !is_readable($keyPath)) {
            Log::error('Clé privée introuvable ou illisible', ['path' => $keyPath]);
            throw new \RuntimeException('Clé privée DocuSign inaccessible.');
        }

        $keyContent = file_get_contents($keyPath);
        if ($keyContent === false) {
            Log::error('Échec de la lecture du fichier de clé privée', ['path' => $keyPath]);
            throw new \RuntimeException('Impossible de lire la clé privée DocuSign.');
        }

        $this->apiClient->getOAuth()->setOAuthBasePath(config('services.docusign.oauth_base_path'));
        $tokenResponse = $this->apiClient->requestJWTUserToken(
            config('services.docusign.client_id'),
            config('services.docusign.user_id'),
            $keyContent,
            config('services.docusign.scope')
        );
        $this->apiClient->getConfig()->setAccessToken($tokenResponse[0]['access_token']);
    }

    public function sendEnvelope(Contrat $contrat, string $method, array $signersData = []): array
    {
        try {
            // Vérifier la prestation
            if (!$contrat->prestation) {
                Log::error('Prestation introuvable pour le contrat', ['contratId' => $contrat->id]);
                return [
                    'success' => false,
                    'error' => 'Prestation associée introuvable.',
                ];
            }

            // Générer et valider le PDF
            $pdf = $this->generatePdf($contrat);
            if (!$this->validateAnchors($pdf, $signersData)) {
                Log::error('Ancre(s) de signature ou date manquante(s) dans le PDF', ['contratId' => $contrat->id]);
                return [
                    'success' => false,
                    'error' => 'Ancre(s) de signature ou date manquante(s) dans le document PDF.',
                ];
            }

            // Créer le document
            $document = $this->createDocument($contrat, $pdf);

            // Créer les signataires
            $signers = $this->createSigners($contrat, $method, $signersData);
            if (!$signers['success']) {
                return $signers;
            }

            // Créer et envoyer l'enveloppe
            $envelopeResult = $this->createAndSendEnvelope($contrat, $document, $signers['signers']);
            if (!$envelopeResult['success']) {
                return $envelopeResult;
            }

            // Mettre à jour le contrat et la prestation
            $this->updateContractAndPrestation($contrat, $envelopeResult['envelope'], $document, $contrat->prestation, $method);

            return [
                'success' => true,
                'message' => 'Contrat envoyé avec succès !',
            ];
        } catch (ApiException $e) {
            return $this->handleApiException($e);
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

    public function downloadSignedDocument(Contrat $contrat)
    {
        // Vérifier que l'enveloppe et le document existent
        if (!$contrat->docusign_envelope_id || !$contrat->docusign_document_id) {
            Log::error('Enveloppe ou document ID manquant pour le contrat', ['contratId' => $contrat->id]);
            throw new \RuntimeException('Impossible de récupérer le document : ID d\'enveloppe ou de document manquant.');
        }

        try {
            $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($this->apiClient);

            // getDocument returns an \SplFileObject
            $fileObject = $envelopeApi->getDocument(
                config('services.docusign.account_id'),
                $contrat->docusign_document_id,
                $contrat->docusign_envelope_id
            );

            // Get the file path from the SplFileObject
            $filePath = $fileObject->getRealPath();

            if ($filePath && file_exists($filePath)) {
                // Read the content of the file
                $documentContent = file_get_contents($filePath);

                // Stream the content for download
                return response()->streamDownload(function () use ($documentContent) {
                    echo $documentContent;
                }, 'contrat_' . $contrat->id . '_signed.pdf', [
                    'Content-Type' => 'application/pdf',
                    'Content-Length' => strlen($documentContent),
                ]);
            } else {
                throw new \Exception("Fichier  introuvable.");
            }


        } catch (ApiException $e) {
            $errorMessage = 'Erreur lors de la récupération du document signé.';
            if ($e->getCode() === 401) {
                $errorMessage = 'Échec de l\'authentification DocuSign.';
            } elseif ($e->getCode() === 400) {
                $errorMessage = 'Requête invalide pour récupérer le document.';
            } elseif ($e->getCode() === 404) {
                $errorMessage = 'Document ou enveloppe introuvable.';
            }
            Log::error('Erreur DocuSign lors de la récupération du document', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'response' => $e->getResponseBody(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \RuntimeException($errorMessage, $e->getCode() ?: 500, $e);
        } catch (\Exception $e) {
            Log::error('Erreur inattendue lors de la récupération du document', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \RuntimeException('Erreur inattendue lors de la récupération : ' . $e->getMessage(), 500, $e);
        }
    }

    private function createDocument(Contrat $contrat, string $pdf): Document
    {
        return new Document([
            'document_base64' => base64_encode($pdf),
            'name' => 'Contrat_' . $contrat->id . '.pdf',
            'file_extension' => 'pdf',
            'document_id' => '1',
        ]);
    }

    private function createSigners(Contrat $contrat, string $method, array $signersData): array
    {
        $prestation = $contrat->prestation;
        $signers = [];

        // Signataire par défaut (artiste)
        $defaultSigners = [
            [
                'email' => $prestation->contact_artiste,
                'name' => $prestation->nom_representant_legal_artiste,
                'role' => 'artiste',
                'recipient_id' => '1',
                'signature_anchor' => config('services.docusign.signature_artiste_anchor', '/signature-artiste/'),
                'date_anchor' => config('services.docusign.date_artiste_anchor', '/date-artiste/'),
            ],
        ];

        // Ajouter le contractant par défaut si aucune donnée spécifique n'est fournie
        if (empty($signersData)) {
            $defaultSigners[] = [
                'contact' => $prestation->contact_organisateur,
                'name' => $prestation->nom_structure_contractante,
                'role' => 'contractant',
                'recipient_id' => '2',
                'signature_anchor' => config('services.docusign.signature_contractant_anchor', '/signature-contractant/'),
                'date_anchor' => config('services.docusign.date_contractant_anchor', '/date-contractant/'),
            ];
        } else {
            // Ajouter les signataires supplémentaires fournis
            foreach ($signersData as $index => $signerData) {
                $defaultSigners[] = [
                    'contact' => $signerData['contact'] ?? $prestation->contact_organisateur,
                    'name' => $signerData['name'] ?? $prestation->nom_structure_contractante,
                    'role' => $signerData['role'] ?? 'contractant',
                    'recipient_id' => (string)($index + 2),
                    'signature_anchor' => $signerData['signature_anchor'] ?? config('services.docusign.signature_contractant_anchor', '/signature-contractant/'),
                    'date_anchor' => $signerData['date_anchor'] ?? config('services.docusign.date_contractant_anchor', '/date-contractant/'),
                ];
            }
        }

        foreach ($defaultSigners as $index => $signerData) {
            $signerResult = $this->createSingleSigner($signerData, $method, $index + 1);
            if (!$signerResult['success']) {
                return $signerResult;
            }
            $signers[] = $signerResult['signer'];

            if (config('app.debug')) {
                Log::debug('Signer Contact: ' . ($signerData['email'] ?? $signerData['contact'] ?? 'N/A'), [
                    'role' => $signerData['role'],
                    'recipient_id' => $signerData['recipient_id'],
                    'delivery_method' => $signerResult['delivery_method'],
                ]);
            }
        }

        return ['success' => true, 'signers' => $signers];
    }

    private function determineDeliveryMethod(string $contact, string $suggestedMethod): string
    {
        // Vérifier si le contact est une adresse email
        if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
            if ($suggestedMethod !== 'email') {
                Log::warning('Méthode suggérée incohérente avec le contact. Utilisation de l\'email.', [
                    'contact' => $contact,
                    'suggested_method' => $suggestedMethod,
                ]);
            }
            return 'email';
        }

        // Vérifier si le contact est un numéro de téléphone
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $parsedNumber = $phoneUtil->parse($contact, null);
            if ($phoneUtil->isValidNumber($parsedNumber)) {
                if (!in_array($suggestedMethod, ['sms', 'whatsapp'])) {
                    Log::warning('Méthode suggérée incohérente avec le numéro de téléphone. Utilisation de WhatsApp par défaut.', [
                        'contact' => $contact,
                        'suggested_method' => $suggestedMethod,
                    ]);
                    return 'whatsapp';
                }
                return $suggestedMethod; // Respecter la méthode suggérée si elle est sms ou whatsapp
            }
        } catch (NumberParseException $e) {
            Log::error('Contact invalide pour déterminer la méthode d\'envoi', [
                'contact' => $contact,
                'error' => $e->getMessage(),
            ]);
        }

        // Si le contact n'est ni un email ni un numéro valide
        throw new \InvalidArgumentException('Contact invalide : doit être une adresse email ou un numéro de téléphone valide.');
    }

    private function createSingleSigner(array $signerData, string $suggestedMethod, int $recipientId): array
    {
        $contact = $signerData['contact'] ?? $signerData['email'] ?? null;
        if (!$contact) {
            return [
                'success' => false,
                'error' => 'Contact manquant pour le signataire ' . ($signerData['role'] ?? 'inconnu'),
            ];
        }

        try {
            $deliveryMethod = $this->determineDeliveryMethod($contact, $suggestedMethod);
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }

        $signerConfig = [
            'name' => $signerData['name'],
            'recipient_id' => (string)$recipientId,
            'routing_order' => '1',
        ];

        if ($deliveryMethod === 'email') {
            $signerConfig['email'] = $contact;
        } elseif (in_array($deliveryMethod, ['sms', 'whatsapp'])) {
            $phoneUtil = PhoneNumberUtil::getInstance();
            try {
                $parsedNumber = $phoneUtil->parse($contact, null);
                if (!$phoneUtil->isValidNumber($parsedNumber)) {
                    return [
                        'success' => false,
                        'error' => 'Numéro de téléphone invalide pour ' . ucfirst($deliveryMethod) . '. Format attendu : +33XXXXXXXXX',
                    ];
                }
                $phoneNumber = $phoneUtil->format($parsedNumber, \libphonenumber\PhoneNumberFormat::E164);
                $countryCode = '+' . $parsedNumber->getCountryCode();
                $number = substr($phoneNumber, strlen($countryCode));
                $signerConfig['phone_number'] = [
                    'countryCode' => $countryCode,
                    'number' => $number,
                ];
                if ($deliveryMethod === 'whatsapp') {
                    $signerConfig['deliveryMethod'] = 'WhatsApp';
                } elseif ($deliveryMethod === 'sms') {
                    $signerConfig['deliveryMethod'] = 'SMS';
                }
            } catch (NumberParseException $e) {
                Log::error('Erreur de validation du numéro de téléphone', [
                    'contact' => $contact,
                    'error' => $e->getMessage(),
                ]);
                return [
                    'success' => false,
                    'error' => 'Erreur de validation du numéro de téléphone pour ' . ucfirst($deliveryMethod) . '.',
                ];
            }
        }

        $signer = new Signer($signerConfig);

        $signTab = new SignHere([
            'anchor_string' => $signerData['signature_anchor'],
            'anchor_units' => 'pixels',
            'anchor_x_offset' => '0',
            'anchor_y_offset' => '0',
            'document_id' => '1',
            'page_number' => '1',
            'recipient_id' => (string)$recipientId,
        ]);

        $dateTab = new DateSigned([
            'anchor_string' => $signerData['date_anchor'],
            'anchor_units' => 'pixels',
            'anchor_x_offset' => '0',
            'anchor_y_offset' => '20',
            'document_id' => '1',
            'page_number' => '1',
            'recipient_id' => (string)$recipientId,
        ]);

        $signer->setTabs(new Tabs([
            'sign_here_tabs' => [$signTab],
            'date_signed_tabs' => [$dateTab],
        ]));

        return [
            'success' => true,
            'signer' => $signer,
            'delivery_method' => $deliveryMethod,
        ];
    }

    private function createAndSendEnvelope(Contrat $contrat, Document $document, array $signers): array
    {
        $envelopeDefinition = new EnvelopeDefinition([
            'email_subject' => 'Signature du contrat #' . $contrat->id,
            'documents' => [$document],
            'recipients' => new \DocuSign\eSign\Model\Recipients([
                'signers' => $signers,
            ]),
            'status' => 'sent',
        ]);

        $envelopeApi = new \DocuSign\eSign\Api\EnvelopesApi($this->apiClient);
        $envelope = $envelopeApi->createEnvelope(config('services.docusign.account_id'), $envelopeDefinition);

        return ['success' => true, 'envelope' => $envelope];
    }

    private function updateContractAndPrestation(Contrat $contrat, $envelope, Document $document, $prestation, string $method): void
    {
        $contrat->status = 'pending';
        $contrat->docusign_envelope_id = $envelope->getEnvelopeId();
        $contrat->docusign_document_id = $document->getDocumentId();
        $contrat->signature_artiste_representant = true;
        $contrat->save();

        if ($prestation->contact_organisateur !== $contrat->prestation->contact_organisateur) {
            if (config('app.debug')) {
                Log::info('Mise à jour de contact_organisateur', [
                    'prestationId' => $prestation->id,
                    'old_contact' => $prestation->contact_organisateur,
                    'new_contact' => $contrat->prestation->contact_organisateur,
                ]);
            }
            $prestation->contact_organisateur = $contrat->prestation->contact_organisateur;
            $prestation->save();
        }
    }

    private function handleApiException(ApiException $e): array
    {
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
    }

    private function generatePdf(Contrat $contrat): string
    {
        $qrCodeUrl = URL::temporarySignedRoute(
            name : 'contrats.download_pdf',
            expiration:  now()->addDays(7),
            parameters : ['contrat' => $contrat->id] ,
            absolute: true,
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

    private function validateAnchors(string $pdfContent, array $signersData): bool
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseContent($pdfContent);
            $text = $pdf->getText();

            $anchors = [
                config('services.docusign.signature_artiste_anchor', '/signature-artiste/') => 'signature artiste',
                config('services.docusign.date_artiste_anchor', '/date-artiste/') => 'date artiste',
            ];

            // Ajouter les ancres des signataires supplémentaires
            if (empty($signersData)) {
                $anchors[config('services.docusign.signature_contractant_anchor', '/signature-contractant/')] = 'signature contractant';
                $anchors[config('services.docusign.date_contractant_anchor', '/date-contractant/')] = 'date contractant';
            } else {
                foreach ($signersData as $signerData) {
                    $anchors[$signerData['signature_anchor'] ?? config('services.docusign.signature_contractant_anchor', '/signature-contractant/')] = 'signature ' . ($signerData['role'] ?? 'contractant');
                    $anchors[$signerData['date_anchor'] ?? config('services.docusign.date_contractant_anchor', '/date-contractant/')] = 'date ' . ($signerData['role'] ?? 'contractant');
                }
            }

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

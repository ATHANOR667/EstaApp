<?php

namespace App\Services;

use App\Models\Contrat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Smalot\PdfParser\Parser;

class ZapSignService
{
    private string $apiUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiUrl = rtrim(config('services.zapsign.api_url', 'https://api.zapsign.com.br/api/v1/'), '/');
        $this->apiKey = config('services.zapsign.api_key');

        if (empty($this->apiKey)) {
            throw new \RuntimeException("Configuration ZapSign invalide : clé API manquante.");
        }
    }

    /**
     * Envoi d’un document à signer
     *
     * @param Contrat $contrat
     * @param string $preferredMethod  email | sms | whatsapp
     * @param array $signersData       signataires supplémentaires optionnels
     * @return array
     */
    public function sendEnvelope(Contrat $contrat, string $preferredMethod, array $signersData = []): array
    {
        try {
            if (!$contrat->prestation) {
                return ['success' => false, 'error' => 'Prestation associée introuvable.'];
            }

            // Générer le PDF
            $pdf = $this->generatePdf($contrat);

            // Vérifier les ancres
            if (!$this->validateAnchors($pdf, $signersData)) {
                return ['success' => false, 'error' => 'Ancre(s) manquante(s) dans le PDF.'];
            }

            // Créer le document ZapSign
            $uploadResponse = Http::withHeaders([
                'Authorization' => "Api-Key {$this->apiKey}",
            ])->post("{$this->apiUrl}/docs/", [
                'name' => "Contrat #{$contrat->id}",
                'base64_pdf' => base64_encode($pdf),
            ]);

            if (!$uploadResponse->successful()) {
                Log::error("Erreur création ZapSign", ['response' => $uploadResponse->body()]);
                return ['success' => false, 'error' => 'Erreur ZapSign lors de la création du document.'];
            }

            $doc = $uploadResponse->json();
            $docToken = $doc['token'];

            // Ajouter les signataires avec fallback
            foreach ($this->buildSigners($contrat, $preferredMethod, $signersData) as $signer) {
                $signerResponse = Http::withHeaders([
                    'Authorization' => "Api-Key {$this->apiKey}",
                ])->post("{$this->apiUrl}/docs/{$docToken}/signers/", $signer);

                if (!$signerResponse->successful()) {
                    Log::error("Erreur ajout signataire ZapSign", [
                        'signer' => $signer,
                        'response' => $signerResponse->body(),
                    ]);
                    return ['success' => false, 'error' => 'Impossible d’ajouter un signataire.'];
                }
            }

            // Réutilisation du champ déjà existant
            $contrat->status = 'pending';
            $contrat->docusign_document_id = $docToken;
            $contrat->save();

            return ['success' => true, 'message' => 'Contrat envoyé avec succès via ZapSign.'];
        } catch (\Exception $e) {
            Log::error('Erreur ZapSign détaillée', [
                'msg' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function downloadSignedDocument(Contrat $contrat): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$contrat->docusign_document_id) {
            throw new \RuntimeException("Impossible de récupérer : document_id manquant.");
        }

        $response = Http::withHeaders([
            'Authorization' => "Api-Key {$this->apiKey}",
        ])->get("{$this->apiUrl}/docs/{$contrat->docusign_document_id}/download/");

        if (!$response->successful()) {
            throw new \RuntimeException("Impossible de télécharger le document signé.");
        }

        $content = $response->body();

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, "contrat_{$contrat->id}_signed.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Préparation des signataires avec fallback canal
     */
    private function buildSigners(Contrat $contrat, string $preferredMethod, array $signersData): array
    {
        $prestation = $contrat->prestation;

        $defaultSigners = [
            [
                'name'  => $prestation->nom_representant_legal_artiste,
                'email' => $prestation->contact_artiste,
                'phone' => $prestation->telephone_artiste ?? null,
            ],
            [
                'name'  => $prestation->nom_structure_contractante,
                'email' => $prestation->contact_organisateur,
                'phone' => $prestation->telephone_organisateur ?? null,
            ],
        ];

        if (!empty($signersData)) {
            foreach ($signersData as $s) {
                $defaultSigners[] = [
                    'name'  => $s['name'],
                    'email' => $s['email'] ?? null,
                    'phone' => $s['phone'] ?? null,
                ];
            }
        }

        return array_map(fn($s) => $this->mapSignerChannel($s, $preferredMethod), $defaultSigners);
    }

    /**
     * Sélection du canal avec fallback
     */
    private function mapSignerChannel(array $signer, string $preferredMethod): array
    {
        $hasEmail = !empty($signer['email']);
        $hasPhone = !empty($signer['phone']);

        $authMode = 'email'; // défaut
        $sendEmail = false;
        $sendSms = false;
        $sendWhatsapp = false;

        switch ($preferredMethod) {
            case 'whatsapp':
                if ($hasPhone) {
                    $authMode = 'whatsapp';
                    $sendWhatsapp = true;
                } elseif ($hasEmail) {
                    $authMode = 'email';
                    $sendEmail = true;
                }
                break;

            case 'sms':
                if ($hasPhone) {
                    $authMode = 'sms';
                    $sendSms = true;
                } elseif ($hasEmail) {
                    $authMode = 'email';
                    $sendEmail = true;
                }
                break;

            case 'email':
            default:
                if ($hasEmail) {
                    $authMode = 'email';
                    $sendEmail = true;
                } elseif ($hasPhone) {
                    // fallback téléphone → sms
                    $authMode = 'sms';
                    $sendSms = true;
                }
                break;
        }

        return [
            'name' => $signer['name'],
            'email' => $signer['email'],
            'phone' => $signer['phone'],
            'auth_mode' => $authMode,
            'send_email' => $sendEmail,
            'send_sms' => $sendSms,
            'send_whatsapp' => $sendWhatsapp,
        ];
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

    private function validateAnchors(string $pdfContent, array $signersData): bool
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseContent($pdfContent);
            $text = $pdf->getText();

            $anchors = [
                '/signature-artiste/',
                '/date-artiste/',
                '/signature-contractant/',
                '/date-contractant/',
            ];

            foreach ($anchors as $anchor) {
                if (substr_count($text, $anchor) !== 1) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Erreur analyse PDF", ['msg' => $e->getMessage()]);
            return false;
        }
    }
}

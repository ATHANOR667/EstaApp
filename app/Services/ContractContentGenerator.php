<?php

namespace App\Services;

use App\Models\Prestation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class ContractContentGenerator
{
    private const MAX_RETRIES = 3;
    private const MAX_DURATION_HOURS = 24;
    private const MIN_DURATION_MINUTES = 10;
    private const MIN_DURATION_PER_SET_MINUTES = 5;
    private const MAX_PAYMENT_DUE_DAYS = 7;

    private AiCallService $aiCallService;
    private MarkdownRenderer $markdownRenderer;

    public function __construct(AiCallService $aiCallService, MarkdownRenderer $markdownRenderer)
    {
        $this->aiCallService = $aiCallService;
        $this->markdownRenderer = $markdownRenderer;
    }

    /**
     * Génère le contenu d'un contrat à partir d'une prestation.
     *
     * @param Prestation $prestation Les données de la prestation
     * @param string $language Langue du contrat (par défaut : 'fr')
     * @return array Résultat avec succès, contenu Markdown et HTML
     */
    public function generateContent(Prestation $prestation, string $language = 'fr'): array
    {
        try {
            // Étape 1 : Validation des données
            $validationResult = $this->validatePrestation($prestation);
            if (!$validationResult['success']) {
                $this->logError('Validation échouée', $prestation->id, $validationResult['error']);
                return $validationResult;
            }

            // Étape 2 : Création des placeholders pour données sensibles
            $placeholders = $this->buildPlaceholders($prestation);

            // Étape 3 : Construction des données structurées du contrat
            $contractDataResult = $this->buildContractData($prestation);
            if (!$contractDataResult['success']) {
                $this->logError('Construction des données échouée', $prestation->id, $contractDataResult['error']);
                return $contractDataResult;
            }

            // Étape 4 : Génération du contenu par l'IA
            $aiContentResult = $this->generateAiContent($contractDataResult['data'], $language);
            if (!$aiContentResult['success']) {
                $this->logError('Génération IA échouée', $prestation->id, $aiContentResult['error']);
                return $aiContentResult;
            }

            $markdownContent = $aiContentResult['markdown'];

            // Étape 5 : Validation stricte du contenu généré
            $contentValidation = $this->validateContentStructure($markdownContent, $prestation->id);
            if (!$contentValidation['success']) {
                $this->logError('Validation du contenu échouée', $prestation->id, $contentValidation['error']);
                return $contentValidation;
            }

            // Étape 6 : Validation des placeholders dans le contenu généré
            $placeholderValidation = $this->validatePlaceholders($markdownContent, $placeholders, $prestation->id);
            if (!$placeholderValidation['success']) {
                $this->logError('Validation des placeholders échouée', $prestation->id, $placeholderValidation['error']);
                return $placeholderValidation;
            }

            // Étape 7 : Remplacement des placeholders par les données réelles
            $finalMarkdown = str_replace(
                array_keys($placeholders),
                array_values($placeholders),
                $markdownContent
            );

            // Étape 8 : Validation des sections non vides
            $sectionValidation = $this->validateSections($finalMarkdown, $prestation->id);
            if (!$sectionValidation['success']) {
                $this->logError('Validation des sections échouée', $prestation->id, $sectionValidation['error']);
                return $sectionValidation;
            }

            // Étape 9 : Conversion en HTML
            $htmlContent = $this->markdownRenderer->toHtml($finalMarkdown);

            return [
                'success' => true,
                'markdown' => $finalMarkdown,
                'html' => $htmlContent,
            ];
        } catch (\Exception $e) {
            $this->logError('Erreur inattendue', $prestation->id, $e->getMessage());
            return [
                'success' => false,
                'error' => 'Erreur lors de la génération du contrat : ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Valide les champs obligatoires et la cohérence des données de la prestation.
     *
     * @param Prestation $prestation
     * @return array Résultat de la validation
     */
    private function validatePrestation(Prestation $prestation): array
    {
        $requiredFields = [
            'artiste.nom' => trim($prestation->artiste->nom ?? ''),
            'lieu_prestation' => trim($prestation->lieu_prestation ?? ''),
            'nom_structure_contractante' => trim($prestation->nom_structure_contractante ?? ''),
            'date_prestation' => $prestation->date_prestation,
            'heure_debut_prestation' => trim($prestation->heure_debut_prestation ?? ''),
            'heure_fin_prevue' => trim($prestation->heure_fin_prevue ?? ''),
        ];

        $missingFields = array_keys(array_filter($requiredFields, fn($value) => empty($value)));
        if ($missingFields) {
            return [
                'success' => false,
                'error' => 'Champs obligatoires manquants : ' . implode(', ', $missingFields),
            ];
        }

        $startTimeResult = $this->normalizeTime($prestation->heure_debut_prestation);
        if (!$startTimeResult['success']) {
            return $startTimeResult;
        }
        $endTimeResult = $this->normalizeTime($prestation->heure_fin_prevue);
        if (!$endTimeResult['success']) {
            return $endTimeResult;
        }

        $startTime = Carbon::parse("{$prestation->date_prestation->format('Y-m-d')} {$startTimeResult['time']}");
        $endTime = Carbon::parse("{$prestation->date_prestation->format('Y-m-d')} {$endTimeResult['time']}");
        if ($endTime < $startTime) {
            $endTime->addDay();
        }

        if ($endTime <= $startTime) {
            return [
                'success' => false,
                'error' => 'L’heure de fin doit être postérieure à l’heure de début.',
            ];
        }

        $durationHours = $endTime->diffInHours($startTime);
        if ($durationHours > self::MAX_DURATION_HOURS) {
            return [
                'success' => false,
                'error' => "La durée de la prestation ($durationHours heures) excède la limite de " . self::MAX_DURATION_HOURS . " heures.",
            ];
        }

        return $this->validateOptionalFields($prestation);
    }

    /**
     * Normalise une chaîne de temps au format HH:MM.
     *
     * @param string $time
     * @return array Résultat avec la chaîne normalisée ou une erreur
     */
    private function normalizeTime(string $time): array
    {
        $time = trim($time);
        if (preg_match('/^(\d{2}):(\d{2})(:\d{2})?$/', $time, $matches)) {
            return [
                'success' => true,
                'time' => "{$matches[1]}:{$matches[2]}",
            ];
        }

        try {
            $parsed = Carbon::parse($time);
            return [
                'success' => true,
                'time' => $parsed->format('H:i'),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Format d’heure invalide : $time",
            ];
        }
    }

    /**
     * Valide les champs optionnels de la prestation.
     *
     * @param Prestation $prestation
     * @return array Résultat de la validation
     */
    private function validateOptionalFields(Prestation $prestation): array
    {
        if (!empty($prestation->duree_effective_performance)) {
            $duration = (int) $prestation->duree_effective_performance;
            if ($duration < self::MIN_DURATION_MINUTES) {
                return [
                    'success' => false,
                    'error' => "La durée effective ($duration minutes) est inférieure à " . self::MIN_DURATION_MINUTES . " minutes.",
                ];
            }

            if (!empty($prestation->nombre_sets_morceaux)) {
                $sets = (int) $prestation->nombre_sets_morceaux;
                if ($sets > 0 && $duration / $sets < self::MIN_DURATION_PER_SET_MINUTES) {
                    return [
                        'success' => false,
                        'error' => "La durée par set (" . ($duration / $sets) . " minutes) est inférieure à " . self::MIN_DURATION_PER_SET_MINUTES . " minutes.",
                    ];
                }
            }
        }

        if (!empty($prestation->date_limite_paiement_solde)) {
            $paymentDueDate = Carbon::parse($prestation->date_limite_paiement_solde);
            $maxDueDate = $prestation->date_prestation->copy()->addDays(self::MAX_PAYMENT_DUE_DAYS);
            if ($paymentDueDate->gt($maxDueDate)) {
                return [
                    'success' => false,
                    'error' => "La date limite de paiement (" . $paymentDueDate->format('Y-m-d') . ") excède " . self::MAX_PAYMENT_DUE_DAYS . " jours après la prestation (" . $maxDueDate->format('Y-m-d') . ").",
                ];
            }
        }

        return ['success' => true];
    }

    /**
     * Construit les placeholders pour les données sensibles.
     *
     * @param Prestation $prestation
     * @return array Liste des placeholders et leurs valeurs réelles
     */
    private function buildPlaceholders(Prestation $prestation): array
    {
        $placeholders = [
            '{ARTIST_NAME}' => trim($prestation->artiste->nom),
            '{EVENT_LOCATION}' => trim($prestation->lieu_prestation),
            '{ORGANIZER}' => trim($prestation->nom_structure_contractante),
            '{AGENT}' => trim($prestation->nom_representant_legal_artiste ?? $prestation->artiste->nom),
        ];

        Log::debug('Placeholders générés', [
            'prestation_id' => $prestation->id,
            'placeholders' => array_keys($placeholders),
        ]);

        return $placeholders;
    }

    /**
     * Construit les données structurées du contrat avec des descriptions lisibles.
     *
     * @param Prestation $prestation
     * @return array Données structurées ou erreur
     */
    private function buildContractData(Prestation $prestation): array
    {
        $date = $prestation->date_prestation->format('Y-m-d');
        $startTimeResult = $this->normalizeTime($prestation->heure_debut_prestation);
        $endTimeResult = $this->normalizeTime($prestation->heure_fin_prevue);

        $startTime = Carbon::parse("$date {$startTimeResult['time']}");
        $endTime = Carbon::parse("$date {$endTimeResult['time']}");
        if ($endTime < $startTime) {
            $endTime->addDay();
        }

        $data = [
            'prestation_id' => $prestation->id,
            'artist_name' => '{ARTIST_NAME}',
            'event_location' => '{EVENT_LOCATION}',
            'nom_representant_legal_artiste' => '{AGENT}',
            'nom_structure_contractante' => '{ORGANIZER}',
            'currency' => 'FCFA',
            'applicable_law' => 'Loi camerounaise',
            'jurisdiction' => 'Tribunaux compétents du Cameroun',
            'start_time' => $startTime->format('d/m/Y à H\hi'),
            'end_time' => $endTime->format('d/m/Y à H\hi'),
        ];

        $fieldDefinitions = [
            'type_evenement' => fn($value) => trim($value) ?: null,
            'montant_total_cachet' => fn($value) => is_numeric($value) ? number_format($value, 0, ',', ' ') . ' FCFA' : null,
            'montant_avance' => fn($value) => is_numeric($value) ? number_format($value, 0, ',', ' ') . ' FCFA' : null,
            'date_limite_paiement_solde' => fn($value) => Carbon::parse($value)->format('d/m/Y'),
            'modalites_paiement' => fn($value) => trim($value) ?: null,
            'duree_effective_performance' => fn($value) => "$value minutes",
            'nombre_sets_morceaux' => fn($value) => (int) $value,
            'frais_annexes_transport' => fn($value) => trim($value) ?: null,
            'frais_annexes_hebergement' => fn($value) => trim($value) ?: null,
            'frais_annexes_restauration' => fn($value) => trim($value) ?: null,
            'frais_annexes_per_diem' => fn($value) => trim($value) ?: null,
            'frais_annexes_autres' => fn($value) => trim($value) ?: null,
            'materiel_fourni_organisateur' => fn($value) => trim($value) ?: null,
            'materiel_apporte_artiste' => fn($value) => trim($value) ?: null,
            'besoins_techniques' => fn($value) => trim($value) ?: null,
            'droits_image' => fn($value) => trim($value) ?: null,
            'mention_artiste_supports_communication' => fn($value) => trim($value) ?: null,
            'interdiction_captation_audio_video' => fn($value) => $this->formatCaptationRule($value),
            'clause_annulation' => fn($value) => trim($value) ?: null,
            'responsabilite_force_majeure' => fn($value) => trim($value) ?: null,
            'assurance_securite_lieu_par' => fn($value) => trim($value) ?: null,
            'engagement_ponctualite_presence' => fn($value) => trim($value) ?: null,
            'observations_particulieres' => fn($value) => trim($value) ?: null,
        ];

        foreach ($fieldDefinitions as $field => $transformer) {
            $value = trim($prestation->$field ?? '');
            if (!empty($value)) {
                $transformed = $transformer($value);
                if ($transformed !== null) {
                    $data[$field] = $transformed;
                }
            }
        }

        return ['success' => true, 'data' => $data];
    }

    /**
     * Formate la règle de captation audio/vidéo.
     *
     * @param string $value
     * @return string|null Règle formatée
     */
    private function formatCaptationRule(string $value): ?string
    {
        $value = trim($value);
        return match ($value) {
            'autorisee' => 'Captation audio/vidéo autorisée pour le public à des fins personnelles uniquement.',
            'interdite' => 'Captation audio/vidéo strictement interdite sans accord préalable écrit de l’Artiste.',
            'partielle' => 'Captation autorisée pour le public à des fins personnelles uniquement ; interdite pour l’Organisateur et tiers sans accord préalable écrit.',
            default => null,
        };
    }

    /**
     * Génère le contenu du contrat via l'IA avec gestion des réessais.
     *
     * @param array $data Données structurées du contrat
     * @param string $language Langue du contrat
     * @return array Résultat avec contenu Markdown ou erreur
     */
    private function generateAiContent(array $data, string $language): array
    {
        for ($retryCount = 0; $retryCount < self::MAX_RETRIES; $retryCount++) {
            try {
                $prompt = $this->buildPrompt($data, $language);
                $payload = [
                    'model' => 'grok-4',
                    'messages' => $prompt,
                    'stream' => false,
                    'max_tokens' => 3000,
                    'temperature' => 0.3,
                ];

                $response = $this->aiCallService->callApi($payload);
                if (empty($response) || trim($response) === '') {
                    $this->logWarning('Réponse IA vide', $data['prestation_id'], 'Retry ' . ($retryCount + 1));
                    continue;
                }

                // Vérification de base de la structure (présence de sections)
                if (!preg_match('/^## .+/m', $response)) {
                    $this->logWarning('Réponse IA non structurée', $data['prestation_id'], 'Retry ' . ($retryCount + 1) . ', Response: ' . substr($response, 0, 500));
                    continue;
                }

                return [
                    'success' => true,
                    'markdown' => $response,
                ];
            } catch (\Exception $e) {
                $this->logWarning('Échec IA', $data['prestation_id'], $e->getMessage() . ' (Retry ' . ($retryCount + 1) . ')');
            }
        }

        return [
            'success' => false,
            'error' => 'Échec de la génération IA après ' . self::MAX_RETRIES . ' tentatives.',
        ];
    }

    /**
     * Construit le prompt pour l'IA avec les sections et champs.
     *
     * @param array $data Données du contrat
     * @param string $language Langue du contrat
     * @return array Messages du prompt (system et user)
     */
    private function buildPrompt(array $data, string $language): array
    {
        $sectionDefinitions = [
            'Introduction et Parties' => [
                'fields' => ['artist_name', 'nom_representant_legal_artiste', 'nom_structure_contractante'],
                'required' => true,
            ],
            'Objet' => [
                'fields' => ['event_location', 'type_evenement'],
                'required' => true,
            ],
            'Rémunération' => [
                'fields' => ['montant_total_cachet', 'montant_avance'],
                'required' => false,
            ],
            'Modalités de paiement' => [
                'fields' => ['modalites_paiement', 'date_limite_paiement_solde'],
                'required' => false,
            ],
            'Conditions de prestation' => [
                'fields' => ['start_time', 'end_time', 'duree_effective_performance', 'nombre_sets_morceaux', 'engagement_ponctualite_presence'],
                'required' => false,
            ],
            'Frais annexes' => [
                'fields' => ['frais_annexes_transport', 'frais_annexes_hebergement', 'frais_annexes_restauration', 'frais_annexes_per_diem', 'frais_annexes_autres'],
                'required' => false,
            ],
            'Clauses techniques' => [
                'fields' => ['materiel_fourni_organisateur', 'materiel_apporte_artiste', 'besoins_techniques'],
                'required' => false,
            ],
            'Sécurité et assurance' => [
                'fields' => ['assurance_securite_lieu_par'],
                'required' => false,
            ],
            'Annulation et force majeure' => [
                'fields' => ['clause_annulation', 'responsabilite_force_majeure'],
                'required' => false,
            ],
            'Droits à l’image et communication' => [
                'fields' => ['droits_image', 'mention_artiste_supports_communication'],
                'required' => false,
            ],
            'Restrictions audiovisuelles' => [
                'fields' => ['interdiction_captation_audio_video'],
                'required' => false,
            ],
            'Observations particulières' => [
                'fields' => ['observations_particulieres'],
                'required' => false,
            ],
            'Dispositions finales' => [
                'fields' => ['applicable_law', 'jurisdiction'],
                'required' => true,
            ],
        ];

        // Filtrer les sections actives
        $activeSections = array_filter(
            $sectionDefinitions,
            fn($section, $title) => $section['required'] ||
                count(array_intersect($section['fields'], array_keys(array_filter($data, fn($v) => !is_null($v))))) > 0,
            ARRAY_FILTER_USE_BOTH
        );

        // Construire la structure des sections pour le prompt
        $sectionsPrompt = '';
        foreach ($activeSections as $title => $section) {
            if ($title === 'Rémunération' && !isset($data['montant_total_cachet'])) {
                continue;
            }
            $sectionsPrompt .= "## $title\nChamps : " . implode(', ', $section['fields']) . "\n";
        }

        $systemMessage = "Tu es un rédacteur de contrats spécialisé en événements artistiques. Rédige un contrat en Markdown, en {$language}, avec un ton professionnel, formel et précis, comme un expert juridique. Structure le contrat en sections avec des titres (##) et rédige chaque section en paragraphes complets et cohérents, comme dans un contrat juridique standard. Évite les listes à puces. Utilise les données JSON fournies pour remplir les sections, en remplaçant les placeholders (ex. {ARTIST_NAME}) par les valeurs correspondantes. Les sections obligatoires (Introduction et Parties, Objet, Dispositions finales) doivent TOUJOURS être incluses avec un contenu significatif. Omet les sections non obligatoires si elles n’ont pas de données pertinentes. Vérifie la cohérence des données (ex. nombre de sets maximum 10, durée par set ≥ 5 minutes). Utilise la devise FCFA. Mentionne 'Loi camerounaise' dans 'Dispositions finales'. Évite les termes vagues. Pas de signatures ni de dates. Data : " . json_encode($data, JSON_UNESCAPED_UNICODE);

        $userMessage = "Rédige un contrat en Markdown en suivant les sections et champs ci-dessous. Chaque section doit être rédigée en paragraphes complets, sans listes à puces, avec un ton juridique formel. Remplace les placeholders par les données JSON. Omet les sections non obligatoires sans données pertinentes. Sections :\n$sectionsPrompt\nData : " . json_encode($data, JSON_UNESCAPED_UNICODE);

        return [
            ['role' => 'system', 'content' => $systemMessage],
            ['role' => 'user', 'content' => $userMessage],
        ];
    }

    /**
     * Valide la structure du contenu généré par l’IA.
     *
     * @param string $markdownContent Contenu Markdown généré
     * @param int $prestationId ID de la prestation
     * @return array Résultat de la validation
     */
    private function validateContentStructure(string $markdownContent, int $prestationId): array
    {
        $lines = explode("\n", $markdownContent);
        $sections = [];
        $currentSection = null;
        $sectionContent = [];

        // Parser les sections et leur contenu
        foreach ($lines as $line) {
            if (preg_match('/^##\s+(.+)/', $line, $matches)) {
                if ($currentSection) {
                    $sections[$currentSection] = trim(implode("\n", $sectionContent));
                    $sectionContent = [];
                }
                $currentSection = $matches[1];
            } elseif ($currentSection) {
                $sectionContent[] = $line;
            }
        }
        if ($currentSection) {
            $sections[$currentSection] = trim(implode("\n", $sectionContent));
        }

        // Vérifier les sections obligatoires
        $requiredSections = ['Introduction et Parties', 'Objet', 'Dispositions finales'];
        $missingSections = array_diff($requiredSections, array_keys($sections));
        if ($missingSections) {
            $this->logWarning('Sections obligatoires manquantes', $prestationId, 'Sections manquantes : ' . implode(', ', $missingSections));
            return [
                'success' => false,
                'error' => 'Sections obligatoires manquantes : ' . implode(', ', $missingSections),
            ];
        }

        // Vérifier que chaque section a du contenu significatif (au moins 10 caractères)
        foreach ($sections as $title => $content) {
            if (strlen(trim($content)) < 10) {
                $this->logWarning('Section sans contenu significatif', $prestationId, "Section '$title' n’a pas de contenu significatif. Contenu : " . substr($content, 0, 500));
                return [
                    'success' => false,
                    'error' => "La section '$title' ne contient pas de contenu significatif (minimum 10 caractères).",
                ];
            }
        }

        return ['success' => true];
    }

    /**
     * Valide la présence des placeholders dans le contenu généré.
     *
     * @param string $markdownContent Contenu Markdown généré
     * @param array $placeholders Liste des placeholders attendus
     * @param int $prestationId ID de la prestation
     * @return array Résultat de la validation
     */
    private function validatePlaceholders(string $markdownContent, array $placeholders, int $prestationId): array
    {
        $missingPlaceholders = [];
        foreach (array_keys($placeholders) as $placeholder) {
            if (strpos($markdownContent, $placeholder) === false) {
                $missingPlaceholders[] = $placeholder;
            }
        }

        if ($missingPlaceholders) {
            $this->logWarning('Placeholders manquants', $prestationId, 'Placeholders manquants : ' . implode(', ', $missingPlaceholders));
            return [
                'success' => false,
                'error' => 'Placeholders manquants dans le contenu : ' . implode(', ', $missingPlaceholders),
            ];
        }

        return ['success' => true];
    }

    /**
     * Valide que chaque section contient du contenu significatif sous forme de paragraphes.
     *
     * @param string $markdownContent Contenu Markdown final
     * @param int $prestationId ID de la prestation
     * @return array Résultat de la validation
     */
    private function validateSections(string $markdownContent, int $prestationId): array
    {
        $lines = explode("\n", $markdownContent);
        $sections = [];
        $currentSection = null;
        $sectionContent = [];

        // Parser les sections
        foreach ($lines as $line) {
            if (preg_match('/^##\s+(.+)/', $line, $matches)) {
                if ($currentSection) {
                    $sections[$currentSection] = trim(implode("\n", $sectionContent));
                    $sectionContent = [];
                }
                $currentSection = $matches[1];
            } elseif ($currentSection) {
                $sectionContent[] = $line;
            }
        }
        if ($currentSection) {
            $sections[$currentSection] = trim(implode("\n", $sectionContent));
        }

        // Vérifier que chaque section a du contenu significatif
        foreach ($sections as $title => $content) {
            if (strlen(trim($content)) < 10) {
                $this->logWarning('Section sans contenu significatif', $prestationId, "Section '$title' n’a pas de contenu significatif (minimum 10 caractères). Contenu : " . substr($content, 0, 500));
                return [
                    'success' => false,
                    'error' => "La section '$title' ne contient pas de contenu significatif (minimum 10 caractères).",
                ];
            }
        }

        // Vérifier les sections obligatoires
        $requiredSections = ['Introduction et Parties', 'Objet', 'Dispositions finales'];
        $missingSections = array_diff($requiredSections, array_keys($sections));
        if ($missingSections) {
            $this->logWarning('Sections obligatoires manquantes', $prestationId, 'Sections manquantes : ' . implode(', ', $missingSections));
            return [
                'success' => false,
                'error' => 'Sections obligatoires manquantes : ' . implode(', ', $missingSections),
            ];
        }

        return ['success' => true];
    }

    /**
     * Journalise une erreur avec des détails.
     *
     * @param string $message Message d'erreur
     * @param int $prestationId ID de la prestation
     * @param string $details Détails supplémentaires
     */
    private function logError(string $message, int $prestationId, string $details): void
    {
        Log::error("ContractContentGenerator - $message", [
            'prestation_id' => $prestationId,
            'details' => $details,
        ]);
    }

    /**
     * Journalise un avertissement avec des détails.
     *
     * @param string $message Message d'avertissement
     * @param int $prestationId ID de la prestation
     * @param string $details Détails supplémentaires
     */
    private function logWarning(string $message, int $prestationId, string $details): void
    {
        Log::warning("ContractContentGenerator - $message", [
            'prestation_id' => $prestationId,
            'details' => $details,
        ]);
    }
}

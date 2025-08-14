<?php

namespace App\Services;

use App\Models\Prestation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class ContractContentGenerator
{
    private AiCallService $aiCallService;
    private MarkdownRenderer $markdownRenderer;

    public function __construct(AiCallService $aiCallService, MarkdownRenderer $markdownRenderer)
    {
        $this->aiCallService = $aiCallService;
        $this->markdownRenderer = $markdownRenderer;
    }

    public function generateContent(Prestation $prestation, string $language = 'fr'): array
    {
        try {
            $this->validatePrestation($prestation);
            $placeholders = $this->buildPlaceholders($prestation);
            $data = $this->buildContractData($prestation);
            $markdownContent = $this->generateAiContent($data, $language);

            $this->validatePlaceholders($markdownContent, $prestation->id);
            $markdownContent = str_replace(array_keys($placeholders), array_values($placeholders), $markdownContent);
            $this->validateNonEmptySections($markdownContent, $prestation->id);
            $htmlContent = $this->markdownRenderer->toHtml($markdownContent);

            return [
                'success' => true,
                'markdown' => $markdownContent,
                'html' => $htmlContent,
            ];
        } catch (\Exception $e) {
            Log::error('Erreur génération contrat', [
                'prestation_id' => $prestation->id,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function validatePrestation(Prestation $prestation): void
    {
        $requiredFields = [
            'artiste.nom' => trim($prestation->artiste->nom ?? ''),
            'lieu_prestation' => trim($prestation->lieu_prestation ?? ''),
            'nom_structure_contractante' => trim($prestation->nom_structure_contractante ?? ''),
            'date_prestation' => $prestation->date_prestation ?? null,
            'heure_debut_prestation' => $prestation->heure_debut_prestation ?? null,
            'heure_fin_prevue' => $prestation->heure_fin_prevue ?? null,
        ];

        $missingFields = array_keys(array_filter($requiredFields, fn($value) => empty($value)));
        if ($missingFields) {
            Log::error('Données manquantes', [
                'prestation_id' => $prestation->id,
                'missing_fields' => $missingFields,
            ]);
            throw new \Exception('Données manquantes : ' . implode(', ', $missingFields));
        }

        // Nettoyage et validation des heures
        $startHour = $this->normalizeTime($prestation->heure_debut_prestation);
        $endHour = $this->normalizeTime($prestation->heure_fin_prevue);

        if (!preg_match('/^\d{2}:\d{2}$/', $startHour) || !preg_match('/^\d{2}:\d{2}$/', $endHour)) {
            Log::error('Format heure invalide', [
                'prestation_id' => $prestation->id,
                'heure_debut_prestation' => $startHour,
                'heure_fin_prevue' => $endHour,
            ]);
            throw new \Exception('Format heure invalide : début=' . $startHour . ', fin=' . $endHour);
        }

        $date = $prestation->date_prestation->format('Y-m-d');
        $startTime = Carbon::parse("$date $startHour");
        $endTime = Carbon::parse("$date $endHour");

        if ($endTime < $startTime) {
            $endTime->addDay();
        }

        if ($endTime <= $startTime) {
            throw new \Exception('Heure de fin doit être postérieure à l’heure de début');
        }
    }

    private function normalizeTime(string $time): string
    {
        $time = trim($time);
        if (preg_match('/(\d{2}:\d{2})(:\d{2})?/', $time, $matches)) {
            return $matches[1]; // Retourne HH:MM
        }
        return $time;
    }

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
            'placeholders' => $placeholders,
        ]);

        return $placeholders;
    }

    private function buildContractData(Prestation $prestation): array
    {
        $date = $prestation->date_prestation->format('Y-m-d');
        $startHour = $this->normalizeTime($prestation->heure_debut_prestation);
        $endHour = $this->normalizeTime($prestation->heure_fin_prevue);

        $startTime = Carbon::parse("$date $startHour");
        $endTime = Carbon::parse("$date $endHour");
        if ($endTime < $startTime) {
            $endTime->addDay();
        }

        $data = [
            'artist_name' => '{ARTIST_NAME}',
            'event_location' => '{EVENT_LOCATION}',
            'nom_representant_legal_artiste' => '{AGENT}',
            'nom_structure_contractante' => '{ORGANIZER}',
            'currency' => 'FCFA',
            'applicable_law' => 'Loi camerounaise, conformément aux dispositions de l’OHADA',
            'jurisdiction' => 'Tribunaux compétents du Cameroun',
            'prestation_id' => $prestation->id,
            'start_time' => $startTime->format('d/m/Y à H\hi'),
            'end_time' => $endTime->format('d/m/Y à H\hi'),
        ];

        $optionalFields = [
            'type_evenement' => 'event_type',
            'montant_total_cachet' => fn($value) => number_format($value, 0, ',', ' ') . ' FCFA',
            'montant_avance' => fn($value) => number_format($value, 0, ',', ' ') . ' FCFA',
            'date_limite_paiement_solde' => fn($value) => $value->format('d/m/Y'),
            'modalites_paiement' => fn($value) => $value ?? 'Paiement par virement bancaire dans un délai de 30 jours après la prestation',
            'duree_effective_performance' => fn($value) => "$value minutes",
            'nombre_sets_morceaux' => 'sets_count',
            'frais_annexes_transport' => fn() => 'Pris en charge par l’organisateur',
            'frais_annexes_hebergement' => fn() => 'Pris en charge par l’organisateur',
            'frais_annexes_restauration' => fn() => 'Pris en charge par l’organisateur',
            'frais_annexes_per_diem' => fn() => 'Pris en charge par l’organisateur',
            'frais_annexes_autres' => 'other_fees',
            'materiel_fourni_organisateur' => fn($value) => $value ?? 'Sonorisation professionnelle avec enceintes actives',
            'materiel_apporte_artiste' => fn($value) => $value ?? 'Instruments personnels (ex. guitare, microphone)',
            'besoins_techniques' => fn($value) => $value ?? 'Microphones dynamiques et éclairage LED',
            'droits_image' => fn($value) => $value ?? 'Autorisation pour la promotion de l’événement',
            'mention_artiste_supports_communication' => fn($value) => $value
                ? 'Mention obligatoire de l’artiste sur tous les supports'
                : 'Aucune mention requise',
            'interdiction_captation_audio_video' => fn($value) => $value ?? 'Captation interdite sans accord préalable',
            'clause_annulation' => fn($value) => $value ?? 'Annulation avec préavis de 30 jours, sous réserve de pénalités',
            'responsabilite_force_majeure' => fn($value) => $value ?? 'Exonération en cas de force majeure (OHADA)',
            'assurance_securite_lieu_par' => fn($value) => $value ?? 'Assurance responsabilité civile à la charge de l’organisateur',
            'engagement_ponctualite_presence' => fn($value) => $value
                ? 'Arrivée 1 heure avant la prestation'
                : 'Aucun engagement spécifique',
            'observations_particulieres' => 'observations_particulieres',
        ];

        foreach ($optionalFields as $field => $transformer) {
            if (isset($prestation->$field) && !empty(trim($prestation->$field))) {
                $key = is_string($transformer) ? $transformer : $field;
                $data[$key] = is_callable($transformer) ? $transformer($prestation->$field) : $prestation->$field;
            }
        }

        return $data;
    }

    private function generateAiContent(array $data, string $language): string
    {
        $prompt = $this->buildPrompt($data, $language);
        $payload = [
            'model' => 'grok-4',
            'messages' => $prompt,
            'stream' => false,
            'max_tokens' => 5000,
            'temperature' => 0.7,
        ];

        $response = $this->aiCallService->callApi($payload);
        if (empty($response)) {
            Log::error('Réponse AI vide', ['prestation_id' => $data['prestation_id']]);
            throw new \Exception('Réponse AI vide');
        }

        Log::debug('Réponse AI reçue', [
            'prestation_id' => $data['prestation_id'],
            'response_snippet' => substr($response, 0, 500),
        ]);

        return $response;
    }

    private function buildPrompt(array $data, string $language): array
    {
        $systemMessage = "Tu es un avocat spécialisé en droit du spectacle au Cameroun, maîtrisant l'OHADA. Rédige un contrat artistique professionnel en Markdown, en {$language}, avec un langage juridique formel, conforme à la loi camerounaise (OHADA). Inclure uniquement les sections pertinentes avec les données fournies ou des valeurs génériques si manquantes. Omet les sections vides ou non pertinentes (ex. contacts, statut). **Dans la section 'Introduction et Parties', utilise impérativement la phrase exacte : 'Entre {ARTIST_NAME}, ci-après « l’Artiste », représenté par {AGENT}, et {ORGANIZER}, ci-après « l’Organisateur ».'** Évite les dates de signature ou champs de signature. Utilise la devise FCFA. Fournis des explications juridiques courtes (1-2 phrases) par section. Significations des champs : droits_image (Oui: autorisation complète pour promotion; Non: interdiction; À définir: négociation future); interdiction_captation_audio_video (Oui: interdiction totale; Non: autorisation complète; Partielle: autorisation uniquement pour le public, interdiction pour organisateur/tiers sans accord); modalites_paiement (Avance + Solde: paiement échelonné; Paiement unique: paiement complet; Autre: personnalisé); assurance_securite_lieu_par (Organisateur: à sa charge; Artiste: à sa charge; Autre: tiers).";

        $sections = [
            'Introduction et Parties' => "Utilise impérativement : 'Entre {ARTIST_NAME}, ci-après « l’Artiste », représenté par {AGENT}, et {ORGANIZER}, ci-après « l’Organisateur ».'",

            'Objet' => 'Type d’événement (select: Concert, Mariage, etc.) et lieu. Générique : « Prestation artistique à {EVENT_LOCATION} ».',

            'Rémunération' => 'Cachet, acompte, frais annexes (booléens: transport, hébergement, restauration, per diem; texte: autres frais). Générique : « Frais raisonnables selon usages camerounais ».',

            'Modalités de paiement' => 'Modalités (select: Avance + Solde, Paiement unique, Autre), date limite solde (date). Générique : « Virement dans 30 jours ».',

            'Conditions de prestation' => 'Durée (number: minutes), sets (number), ponctualité (booléen), horaires (date+time: d/m/Y H\hi). Générique : « Durée 60 minutes, début convenu ».',

            'Clauses techniques' => 'Équipement organisateur/artiste, besoins techniques (textarea). Générique : « Sonorisation professionnelle, instruments personnels ».',

            'Sécurité et assurance' => 'Assurance (select: Organisateur, Artiste, Autre). Générique : « Charge organisateur ».',

            'Annulation et force majeure' => 'Annulation (textarea), force majeure (text). Générique : « Préavis 30 jours, exonération force majeure ».',

            'Droits à l’image et communication' => 'Droits image (select: Oui, Non, À définir), mention (booléen). Générique : « Autorisation promotion ».',

            'Restrictions audiovisuelles' => 'Captation (select: Oui, Non, Partielle=public uniquement). Générique : « Interdite sans accord ».',

            'Observations particulières' => 'Notes (textarea). Omettre si vide.',

            'Dispositions finales' => 'Loi applicable (OHADA), juridiction (Cameroun).',
        ];

        $availableSections = array_keys(array_filter($data, fn($value, $key) => !in_array($key, ['artist_name', 'event_location', 'nom_representant_legal_artiste', 'nom_structure_contractante', 'currency', 'applicable_law', 'jurisdiction', 'prestation_id']) && $value !== null, ARRAY_FILTER_USE_BOTH));
        $activeSections = array_filter($sections, fn($key) => $key === 'Introduction et Parties' || $key === 'Objet' || $key === 'Dispositions finales' || count(array_intersect($this->sectionFields($key), $availableSections)) > 0, ARRAY_FILTER_USE_KEY);

        $sectionInstructions = implode("\n- ", array_map(fn($title, $desc) => "**{$title}** : {$desc}", array_keys($activeSections), $activeSections));

        $userMessage = "Rédige un contrat artistique en Markdown avec les sections suivantes :
         \n{$sectionInstructions}\n\nUtilise les données : " .
            json_encode($data, JSON_UNESCAPED_UNICODE) .
            ".\nUtilise les données spécifiques (types : texte, booléen, select)
            ou génériques si manquantes. **Dans la section 'Introduction et Parties',
            utilise impérativement la phrase : 'Entre {ARTIST_NAME}, ci-après « l’Artiste »,
            représenté par {AGENT}, et {ORGANIZER}, ci-après « l’Organisateur ».'**
            Omet sections non pertinentes. Évite sections vides ou clauses non demandées
            (ex. annulation pour maladie). Pas de signatures/dates. Explications juridiques
            : 1-2 phrases.";

        return [
            ['role' => 'system', 'content' => $systemMessage],
            ['role' => 'user', 'content' => $userMessage],
        ];
    }

    private function sectionFields(string $section): array
    {
        return match ($section) {
            'Rémunération' => ['cache_amount', 'advance_amount', 'transport_fees', 'accommodation_fees', 'catering_fees', 'per_diem_fees', 'other_fees'],
            'Modalités de paiement' => ['payment_terms', 'balance_due_date'],
            'Conditions de prestation' => ['duration', 'sets_count', 'punctuality_commitment', 'start_time', 'end_time'],
            'Clauses techniques' => ['organizer_equipment', 'artist_equipment', 'technical_needs'],
            'Sécurité et assurance' => ['insurance'],
            'Annulation et force majeure' => ['cancellation_clause', 'force_majeure'],
            'Droits à l’image et communication' => ['image_rights', 'communication_mention'],
            'Restrictions audiovisuelles' => ['audio_video_restriction'],
            'Observations particulières' => ['observations_particulieres'],
            default => [],
        };
    }

    private function validatePlaceholders(string $markdownContent, int $prestationId): void
    {
        $requiredPlaceholders = ['{ARTIST_NAME}', '{AGENT}', '{ORGANIZER}'];
        $inIntroductionSection = false;
        $foundPlaceholders = [];

        $lines = explode("\n", $markdownContent);
        foreach ($lines as $line) {
            if (preg_match('/^##\s+Introduction et Parties/', $line)) {
                $inIntroductionSection = true;
            } elseif ($inIntroductionSection && preg_match('/^##\s+/', $line)) {
                $inIntroductionSection = false;
            } elseif ($inIntroductionSection) {
                foreach ($requiredPlaceholders as $placeholder) {
                    if (str_contains($line, $placeholder) && !in_array($placeholder, $foundPlaceholders)) {
                        $foundPlaceholders[] = $placeholder;
                    }
                }
            }
        }

        $missingPlaceholders = array_diff($requiredPlaceholders, $foundPlaceholders);
        if ($missingPlaceholders) {
            Log::warning('Placeholders manquants dans Introduction et Parties', [
                'prestation_id' => $prestationId,
                'missing' => $missingPlaceholders,
                'markdown_snippet' => substr($markdownContent, 0, 500),
            ]);
            throw new \Exception('Placeholders manquants dans Introduction et Parties : ' . implode(', ', $missingPlaceholders));
        }
    }

    private function validateNonEmptySections(string $markdown, int $prestationId): void
    {
        $lines = explode("\n", $markdown);
        $currentSection = null;
        $sectionContent = [];
        $emptySections = [];

        foreach ($lines as $line) {
            if (preg_match('/^##\s+(.+)/', $line, $matches)) {
                if ($currentSection && empty(trim(implode('', $sectionContent)))) {
                    $emptySections[] = $currentSection;
                }
                $currentSection = $matches[1];
                $sectionContent = [];
            } elseif ($currentSection) {
                $sectionContent[] = $line;
            }
        }

        if ($currentSection && empty(trim(implode('', $sectionContent)))) {
            $emptySections[] = $currentSection;
        }

        if ($emptySections) {
            Log::error('Sections vides détectées', [
                'prestation_id' => $prestationId,
                'empty_sections' => $emptySections,
            ]);
            throw new \Exception('Sections vides : ' . implode(', ', $emptySections));
        }
    }
}

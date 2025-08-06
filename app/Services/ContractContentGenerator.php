<?php

namespace App\Services;

use App\Models\Prestation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContractContentGenerator
{
    protected $apiKey;
    protected $apiEndpoint = 'https://api.deepseek.com/chat/completions';

    public function __construct()
    {
        $this->apiKey = env('AI_API_KEY');
    }

    public function generateContent(Prestation $prestation): string
    {
        $placeholders = [
            '{ARTIST_NAME}' => $prestation->artiste->nom ?? 'Artiste X',
            '{EVENT_LOCATION}' => $prestation->lieu_prestation ?? 'Lieu Z',
            '{ORGANIZER}' => $prestation->nom_structure_contractante ?? 'Organisateur Y',
            '{AGENT}' => $prestation->nom_representant_legal_artiste ?? 'Agent P',
            '{DATE}' => $prestation->date_prestation ?? 'Date D',
        ];

        $data = [
            'artist_name' => '{ARTIST_NAME}',
            'event_location' => '{EVENT_LOCATION}',
            'nom_representant_legal_artiste' => '{AGENT}',
            'nom_structure_contractante' => '{ORGANIZER}',
            'date_prestation' => '{DATE}',
            'event_type' => $prestation->type_evenement ?? 'N/A',
            'cache_amount' => $prestation->montant_total_cachet ?? 0,
            'duration' => $prestation->duree_effective_performance ?? 0,
            'sets_count' => $prestation->nombre_sets_morceaux ?? 0,
            'payment_terms' => $prestation->modalites_paiement ?? 'N/A',
            'advance_amount' => $prestation->montant_avance ?? 0,
            'balance_due_date' => $prestation->date_limite_paiement_solde ? $prestation->date_limite_paiement_solde->format('d/m/Y') : 'N/A',
            'transport_fees' => $prestation->frais_annexes_transport ? 'Oui' : 'Non',
            'accommodation_fees' => $prestation->frais_annexes_hebergement ? 'Oui' : 'Non',
            'catering_fees' => $prestation->frais_annexes_restauration ? 'Oui' : 'Non',
            'per_diem_fees' => $prestation->frais_annexes_per_diem ? 'Oui' : 'Non',
            'other_fees' => $prestation->frais_annexes_autres ?? 'Aucun',
            'organizer_equipment' => $prestation->materiel_fourni_organisateur ?? 'Aucun',
            'artist_equipment' => $prestation->materiel_apporte_artiste ?? 'Aucun',
            'technical_needs' => $prestation->besoins_techniques ?? 'Aucun',
            'image_rights' => $prestation->droits_image ?? 'N/A',
            'communication_mention' => $prestation->mention_artiste_supports_communication ? 'Oui' : 'Non',
            'audio_video_restriction' => $prestation->interdiction_captation_audio_video ?? 'N/A',
            'cancellation_clause' => $prestation->clause_annulation ?? 'N/A',
            'force_majeure' => $prestation->responsabilite_force_majeure ?? 'N/A',
            'insurance' => $prestation->assurance_securite_lieu_par ?? 'N/A',
            'punctuality_commitment' => $prestation->engagement_ponctualite_presence ? 'Oui' : 'Non',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
            ])->withOptions([
                'verify' => false,
                'timeout' => 200, // Augmenté à 200 secondes
                'connect_timeout' => 10,
            ])->post($this->apiEndpoint, [
                "model" => "deepseek-chat",
                "messages" => [
                    [
                        'role' => 'system',
                        'content' => 'Tu es un avocat spécialisé en droit du spectacle, chargé de rédiger des contrats artistiques complets, clairs et juridiquement solides.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Rédige un contrat artistique en **Markdown** avec les sections suivantes : Objet, Rémunération, Conditions, Clauses Techniques, Sécurité, Annulation, Clauses de Communication. Sois formel, précis, et évite les répétitions inutiles. Utilise les données suivantes : " . json_encode($data),
                    ],
                ],
                "stream" => false,
            ]);

            if ($response->failed()) {
                Log::error('Erreur DeepSeek', ['body' => $response->body()]);
                throw new \Exception("Erreur de génération : " . $response->body());
            }

            $json = $response->json();
            $content = $json['choices'][0]['message']['content'] ?? null;

            if (!$content) {
                throw new \Exception("Contenu vide retourné par l'IA.");
            }

            return str_replace(
                array_keys($placeholders),
                array_values($placeholders),
                $content
            );
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Timeout ou erreur réseau lors de la génération du contrat', ['exception' => $e->getMessage()]);
            throw new \Exception("La génération du contrat a pris trop de temps. Veuillez réessayer plus tard.");
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du contrat IA', ['exception' => $e->getMessage()]);
            throw new \Exception("Erreur lors de la génération du contenu : " . $e->getMessage());
        }
    }
}

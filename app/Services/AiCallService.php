<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCallService
{
    protected string $apiKey;
    protected string $apiEndpoint;
    protected int $timeout = 120;
    protected int $connectTimeout = 5;

    public function __construct()
    {
        $this->apiKey = env('XAI_API_KEY');
        $this->apiEndpoint = env('XAI_API_ENDPOINT', 'https://api.x.ai/v1/chat/completions');
    }

    public function callApi(array $payload): string
    {
        try {

            $payload = array_merge([
                'model' => 'grok-4',
                'max_tokens' => 4000,
                'temperature' => 0.7,
                'stream' => false,
            ], $payload);

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false,
                'timeout' => $this->timeout,
                'connect_timeout' => $this->connectTimeout,
            ])->post($this->apiEndpoint, $payload);

            if ($response->failed()) {
                Log::error('Erreur lors de l\'appel à l\'API Grok 4', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Erreur API Grok 4 : ' . $response->body());
            }

            $content = $response->json()['choices'][0]['message']['content'] ?? null;
            if (!$content) {
                Log::error('Contenu vide retourné par l\'API Grok 4', ['response' => $response->body()]);
                throw new \Exception('Contenu vide retourné par Grok 4.');
            }

            return $content;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Timeout ou erreur réseau lors de l\'appel à l\'API Grok 4', [
                'exception' => $e->getMessage(),
            ]);
            throw new \Exception('La requête à l\'API Grok 4 a pris trop de temps ou a échoué.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'appel à l\'API Grok 4', [
                'exception' => $e->getMessage(),
            ]);
            throw new \Exception('Erreur lors de l\'appel à l\'API Grok 4 : ' . $e->getMessage());
        }
    }
}

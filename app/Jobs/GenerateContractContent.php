<?php

namespace App\Jobs;

use App\Events\ContractContentGenerated;
use App\Events\ContractContentGenerationFailed;
use App\Models\Prestation;
use App\Services\ContractContentGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GenerateContractContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    protected string|int $prestationId;
    protected string|int $userId;

    public function __construct(string|int $prestationId, string|int $userId)
    {
        $this->prestationId = $prestationId;
        $this->userId = $userId;
    }

    public function handle(ContractContentGenerator $generator): void
    {
        try {
            $prestation = Prestation::findOrFail($this->prestationId);
            $result = $generator->generateContent($prestation);

            if (!$result['success']) {
                event(new ContractContentGenerationFailed(['error' => $result['error']], $this->prestationId, $this->userId));
                return;
            }

            $htmlContent = $result['html'] ?: '<p>Contenu généré vide</p>';
            $cacheKey = 'contract_content_' . $this->prestationId . '_' . $this->userId . '_' . time();
            Cache::put($cacheKey, $htmlContent, now()->addMinutes(10));


            event(new ContractContentGenerated($cacheKey, $this->prestationId, $this->userId));
        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du job GenerateContractContent', [
                'prestation_id' => $this->prestationId,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);
            event(new ContractContentGenerationFailed(['error' => $e->getMessage()], $this->prestationId, $this->userId));
        }
    }
}

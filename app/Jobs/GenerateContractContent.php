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
use Illuminate\Support\Facades\Log;
use Parsedown;

class GenerateContractContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $prestationId;
    protected $componentId;

    public $timeout = 200;

    public function __construct($prestationId, $componentId)
    {
        $this->prestationId = $prestationId;
        $this->componentId = $componentId;
    }

    public function handle(): void
    {
        try {
            $prestation = Prestation::findOrFail($this->prestationId);
            $generator = new ContractContentGenerator();
            $markdownContent = $generator->generateContent($prestation);
            $parsedown = new Parsedown();
            $parsedown->setSafeMode(true);
            $htmlContent = $parsedown->text($markdownContent);
            $htmlContent = empty(trim($htmlContent)) ? '<p>Contenu généré vide</p>' : $htmlContent;

            Log::info('Contenu IA généré et converti', [
                'prestationId' => $this->prestationId,
                'markdown_length' => strlen($markdownContent),
                'html_length' => strlen($htmlContent),
                'html_content' => substr($htmlContent, 0, 200)
            ]);

            event(new ContractContentGenerated($htmlContent, $this->componentId));

        } catch (\Exception $e) {
            Log::error('Erreur dans le job de génération', ['error' => $e->getMessage(), 'prestationId' => $this->prestationId]);
            event(new ContractContentGenerationFailed($e->getMessage(), $this->componentId));
        }
    }
}

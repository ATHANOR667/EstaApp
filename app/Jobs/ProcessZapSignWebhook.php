<?php

namespace App\Jobs;

use App\Models\Contrat;
use App\Notifications\ContractStatusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessZapSignWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $documentKey = $this->data['document_key'] ?? null;
        $status = $this->data['status'] ?? null;

        if (!$documentKey || !$status) {
            Log::error('Invalid ZapSign webhook data in job', ['data' => $this->data]);
            return;
        }

        // Find contract by ZapSign document key
        $contrat = Contrat::where('zapsign_document_key', $documentKey)->first();
        if (!$contrat) {
            Log::error('Contract not found for ZapSign document key', ['document_key' => $documentKey]);
            return;
        }

        // Map ZapSign status to contract status
        $statusMap = [
            'waiting_signature' => 'sent',
            'signed' => 'signed',
            'rejected' => 'declined',
            'expired' => 'expired',
        ];

        if (!array_key_exists($status, $statusMap)) {
            Log::warning('Unhandled ZapSign status', [
                'status' => $status,
                'document_key' => $documentKey,
            ]);
            return;
        }

        $newStatus = $statusMap[$status];
        if ($contrat->status !== $newStatus) {
            $contrat->status = $newStatus;
            $contrat->save();
            Log::info('Contract status updated from ZapSign', [
                'contrat_id' => $contrat->id,
                'new_status' => $newStatus,
            ]);

            // Send notification to contact_artiste
            try {
                $contrat->notify(new ContractStatusNotification($contrat, $status));
                Log::info('Notification sent for contract', [
                    'contrat_id' => $contrat->id,
                    'status' => $newStatus,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send notification for ZapSign contract', [
                    'contrat_id' => $contrat->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::info('Contract status unchanged (ZapSign)', [
                'contrat_id' => $contrat->id,
                'status' => $newStatus,
            ]);
        }
    }
}

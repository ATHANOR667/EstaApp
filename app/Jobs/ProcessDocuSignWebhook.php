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

class ProcessDocuSignWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        $envelopeId = $this->data['envelope_id'] ?? null;
        $status = $this->data['status'] ?? null;

        if (!$envelopeId || !$status) {
            Log::error('Invalid webhook data in job', ['data' => $this->data]);
            return;
        }

        // Find contract
        $contrat = Contrat::where('docusign_envelope_id', $envelopeId)->first();
        if (!$contrat) {
            Log::error('Contract not found for envelope', ['envelope_id' => $envelopeId]);
            return;
        }

        // Map DocuSign status to contract status
        $statusMap = [
            'envelope-sent' => 'sent',
            'envelope-delivered' => 'delivered',
            'envelope-completed' => 'signed',
            'envelope-declined' => 'declined',
            'envelope-voided' => 'voided',
        ];

        if (!array_key_exists($status, $statusMap)) {
            Log::warning('Unhandled DocuSign status', [
                'status' => $status,
                'envelope_id' => $envelopeId,
            ]);
            return;
        }

        $newStatus = $statusMap[$status];
        if ($contrat->status !== $newStatus) {
            $contrat->status = $newStatus;
            $contrat->save();
            Log::info('Contract status updated', [
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
                Log::error('Failed to send notification', [
                    'contrat_id' => $contrat->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } else {
            Log::info('Contract status unchanged', [
                'contrat_id' => $contrat->id,
                'status' => $newStatus,
            ]);
        }
    }
}

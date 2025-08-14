<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contrat;
use App\Models\Prestation;
use App\Services\DocuSignService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class DocuSignServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $docuSignService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->docuSignService = new DocuSignService();
    }

    public function test_cannot_send_envelope_if_pending_contract_exists()
    {
        // Créer une prestation
        $prestation = Prestation::factory()->create();
        // Créer un contrat en attente
        Contrat::factory()->create([
            'prestation_id' => $prestation->id,
            'status' => 'pending',
        ]);
        // Créer un nouveau contrat
        $contrat = Contrat::factory()->create([
            'prestation_id' => $prestation->id,
            'status' => 'draft',
        ]);

        $result = $this->docuSignService->sendEnvelope($contrat, 'email');

        $this->assertFalse($result['success']);
        $this->assertEquals('Un autre contrat est en attente de signature pour cette prestation.', $result['error']);
    }

    public function test_invalid_phone_number_for_sms()
    {
        $prestation = Prestation::factory()->create([
            'contact_organisateur' => 'invalid_phone',
            'nom_structure_contractante' => 'Test Organisation',
            'contact_artiste' => 'artiste@example.com',
            'nom_representant_legal_artiste' => 'Artiste Name',
        ]);
        $contrat = Contrat::factory()->create([
            'prestation_id' => $prestation->id,
            'status' => 'draft',
        ]);

        $result = $this->docuSignService->sendEnvelope($contrat, 'sms');

        $this->assertFalse($result['success']);
        $this->assertEquals('Numéro de téléphone invalide pour SMS. Format attendu : +33XXXXXXXXX', $result['error']);
    }

    public function test_successful_email_envelope()
    {
        // Mock de l'API DocuSign pour éviter les appels réels
        $apiClientMock = Mockery::mock(\DocuSign\eSign\Client\ApiClient::class);
        $apiClientMock->shouldReceive('getOAuth->setOAuthBasePath')->once();
        $apiClientMock->shouldReceive('requestJWTUserToken')->andReturn([['access_token' => 'mock_token']]);
        $apiClientMock->shouldReceive('getConfig->setAccessToken')->once();
        $apiClientMock->shouldReceive('getConfig->setHost')->once();

        $envelopeApiMock = Mockery::mock(\DocuSign\eSign\Api\EnvelopesApi::class);
        $envelopeApiMock->shouldReceive('createEnvelope')->andReturn(new \DocuSign\eSign\Model\Envelope(['envelope_id' => 'mock_envelope_id']));

        $this->app->instance(\DocuSign\eSign\Client\ApiClient::class, $apiClientMock);

        $prestation = Prestation::factory()->create([
            'contact_organisateur' => 'organisateur@example.com',
            'nom_structure_contractante' => 'Test Organisation',
            'contact_artiste' => 'artiste@example.com',
            'nom_representant_legal_artiste' => 'Artiste Name',
        ]);
        $contrat = Contrat::factory()->create([
            'prestation_id' => $prestation->id,
            'status' => 'draft',
        ]);

        $result = $this->docuSignService->sendEnvelope($contrat, 'email');

        $this->assertTrue($result['success']);
        $this->assertEquals('Contrat envoyé avec succès via Email !', $result['message']);
        $this->assertEquals('pending', $contrat->fresh()->status);
        $this->assertEquals('mock_envelope_id', $contrat->fresh()->docusign_envelope_id);
    }
}

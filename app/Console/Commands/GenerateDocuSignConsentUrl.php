<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DocuSign\eSign\Client\ApiClient;

class GenerateDocuSignConsentUrl extends Command
{
    protected $signature = 'docusign:generate-consent-url';
    protected $description = 'Generate the DocuSign OAuth consent URL';

    public function handle()
    {
        $clientId = config('services.docusign.client_id');
        $oauthBasePath = config('services.docusign.oauth_base_path');
        $scopes = ['signature', 'impersonation'];
        $redirectUri = 'https://esta-app.thehopecharity.com/callback';

        try {
            $apiClient = new ApiClient();
            $apiClient->getOAuth()->setOAuthBasePath($oauthBasePath);

            $authUri = $apiClient->getAuthorizationURI(
                $client_id = $clientId,
                $scopes = $scopes,
                $redirect_uri = $redirectUri,
                $response_type = 'code',
                $state = 'optional-state'
            );

            $this->info("URL de consentement : {$authUri}");
            $this->info("Ouvrez cette URL dans un navigateur pour donner votre consentement.");
        } catch (\Exception $e) {
            $this->error("Erreur : " . $e->getMessage());
            $this->error("Fichier : " . $e->getFile());
            $this->error("Ligne : " . $e->getLine());
            $this->error("Trace : " . $e->getTraceAsString());
        }
    }
}

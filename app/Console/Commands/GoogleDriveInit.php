<?php

namespace App\Console\Commands;

use Google\Client as GoogleClient;
use Google\Service\Drive;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GoogleDriveInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google:drive-init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and store Google Drive OAuth token (token.json)';

    public function handle(): int
    {
        $credentialsPath = config('services.google_drive.credentials');
        $tokenPath = config('services.google_drive.token');

        if (!$credentialsPath || !File::exists($credentialsPath)) {
            $this->error('Google Drive credentials file not found. Check GOOGLE_DRIVE_CREDENTIALS in .env');
            return Command::FAILURE;
        }

        if (!$tokenPath) {
            $this->error('GOOGLE_DRIVE_TOKEN path is not configured.');
            return Command::FAILURE;
        }

        $client = new GoogleClient();
        $client->setApplicationName(config('app.name', 'Forward Edge') . ' Drive');
        $client->setScopes([Drive::DRIVE]);
        $client->setAuthConfig($credentialsPath);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $authUrl = $client->createAuthUrl();
        $this->info("Open this link in your browser and authorize access:\n{$authUrl}\n");

        $authCode = $this->ask('Paste the authorization code here');
        if (!$authCode) {
            $this->error('No authorization code provided.');
            return Command::FAILURE;
        }

        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        if (isset($accessToken['error'])) {
            $this->error('Error fetching access token: ' . $accessToken['error']);
            return Command::FAILURE;
        }

        $tokenDirectory = dirname($tokenPath);
        if (!File::exists($tokenDirectory)) {
            File::makeDirectory($tokenDirectory, 0755, true);
        }

        File::put($tokenPath, json_encode($accessToken, JSON_PRETTY_PRINT));

        $this->info("Token stored to: {$tokenPath}");

        return Command::SUCCESS;
    }
}

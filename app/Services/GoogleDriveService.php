<?php

namespace App\Services;

use Google_Client;
use Google_Exception;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected ?Google_Service_Drive $drive = null;

    public function isConfigured(): bool
    {
        $config = config('services.google_drive');
        return class_exists(Google_Client::class)
            && !empty($config['credentials'])
            && file_exists($config['credentials'])
            && !empty($config['token'])
            && file_exists($config['token']);
    }

    protected function client(): ?Google_Client
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $client = new Google_Client();
            $client->setApplicationName(config('app.name', 'Forward Edge') . ' Drive Automation');
            $client->setScopes([Google_Service_Drive::DRIVE]);
            $client->setAuthConfig(config('services.google_drive.credentials'));
            $client->setAccessType('offline');
            $client->setPrompt('select_account consent');

            $tokenPath = config('services.google_drive.token');
            $token = json_decode((string) file_get_contents($tokenPath), true);
            $client->setAccessToken($token);

            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                } else {
                    Log::warning('Google Drive token expired and no refresh token available');
                    return null;
                }
            }

            if ($impersonate = config('services.google_drive.impersonate')) {
                $client->setSubject($impersonate);
            }

            return $client;
        } catch (Google_Exception $e) {
            Log::error('Google Drive client init failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    protected function drive(): ?Google_Service_Drive
    {
        if ($this->drive) {
            return $this->drive;
        }

        $client = $this->client();
        if (!$client) {
            return null;
        }

        return $this->drive = new Google_Service_Drive($client);
    }

    public function grantReader(string $folderId, string $email): bool
    {
        if (!$folderId || !$email) {
            return false;
        }

        $service = $this->drive();
        if (!$service) {
            return false;
        }

        try {
            $permission = new Google_Service_Drive_Permission([
                'type' => 'user',
                'role' => 'reader',
                'emailAddress' => $email,
            ]);

            $service->permissions->create($folderId, $permission, [
                'sendNotificationEmail' => (bool) config('services.google_drive.notify', true),
                'emailMessage' => 'Forward Edge just granted you access to your course resources.',
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('Google Drive permission failed', [
                'folder_id' => $folderId,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

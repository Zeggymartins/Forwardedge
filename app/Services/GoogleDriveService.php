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

    public function grantReader(string $folderId, string $email, bool $restrictSharing = true): bool
    {
        if (!$folderId || !$email) {
            return false;
        }

        $service = $this->drive();
        if (!$service) {
            return false;
        }

        try {
            // Create permission with security restrictions
            $permission = new Google_Service_Drive_Permission([
                'type' => 'user',
                'role' => 'reader',
                'emailAddress' => $email,
                'allowFileDiscovery' => false, // Hide from search/discovery
            ]);

            // Grant permission
            $service->permissions->create($folderId, $permission, [
                'sendNotificationEmail' => (bool) config('services.google_drive.notify', true),
                'emailMessage' => 'Forward Edge has granted you access to your course materials. Please log in to the Forward Edge platform to view your content.',
            ]);

            // Apply folder-level security restrictions
            if ($restrictSharing) {
                $this->restrictFolderSharing($folderId);
            }

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

    /**
     * Apply security restrictions to a folder to prevent unauthorized sharing
     */
    protected function restrictFolderSharing(string $folderId): void
    {
        $service = $this->drive();
        if (!$service) {
            return;
        }

        try {
            $file = new \Google_Service_Drive_DriveFile();

            // Prevent readers from copying/downloading
            $file->setCopyRequiresWriterPermission(true);

            // Update the folder with restrictions
            $service->files->update($folderId, $file, [
                'fields' => 'copyRequiresWriterPermission',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to apply folder restrictions', [
                'folder_id' => $folderId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Revoke access for a specific email
     */
    public function revokeAccess(string $folderId, string $email): bool
    {
        $service = $this->drive();
        if (!$service) {
            return false;
        }

        try {
            // List all permissions for the folder
            $permissions = $service->permissions->listPermissions($folderId, [
                'fields' => 'permissions(id,emailAddress)',
            ]);

            // Find and delete the permission for this email
            foreach ($permissions->getPermissions() as $permission) {
                if ($permission->getEmailAddress() === $email) {
                    $service->permissions->delete($folderId, $permission->getId());
                    Log::info('Revoked Google Drive access', [
                        'folder_id' => $folderId,
                        'email' => $email,
                    ]);
                    return true;
                }
            }

            return false;
        } catch (\Throwable $e) {
            Log::error('Failed to revoke Google Drive access', [
                'folder_id' => $folderId,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get the Drive service instance for advanced operations
     */
    public function getDriveService(): ?Google_Service_Drive
    {
        return $this->drive();
    }

    /**
     * Check if a user has access to a folder
     */
    public function hasAccess(string $folderId, string $email): bool
    {
        $service = $this->drive();
        if (!$service) {
            return false;
        }

        try {
            $permissions = $service->permissions->listPermissions($folderId, [
                'fields' => 'permissions(emailAddress)',
            ]);

            foreach ($permissions->getPermissions() as $permission) {
                if ($permission->getEmailAddress() === $email) {
                    return true;
                }
            }

            return false;
        } catch (\Throwable $e) {
            Log::error('Failed to check Google Drive access', [
                'folder_id' => $folderId,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Exception;

class UpdateService
{
    protected $githubRepo = 'pisknk/Barangay-Certifier';
    protected $currentVersion;
    protected $cacheKey = 'app_update_check';
    protected $cacheDuration = 3600; // 1 hour
    
    public function __construct()
    {
        $this->currentVersion = $this->getCurrentVersion();
    }
    
    /**
     * get current app version from version.json
     * if file doesn't exist, create it with default version
     */
    public function getCurrentVersion()
    {
        $versionFile = base_path('version.json');
        
        if (!File::exists($versionFile)) {
            // create version file with default version 1.0.0
            $versionData = [
                'version' => '1.0.0',
                'build' => date('YmdHis'),
                'updated_at' => now()->toDateTimeString()
            ];
            
            File::put($versionFile, json_encode($versionData, JSON_PRETTY_PRINT));
            return '1.0.0';
        }
        
        try {
            $versionData = json_decode(File::get($versionFile), true);
            return $versionData['version'] ?? '1.0.0';
        } catch (Exception $e) {
            Log::error('Error reading version file: ' . $e->getMessage());
            return '1.0.0';
        }
    }
    
    /**
     * check if update is available
     */
    public function checkForUpdates()
    {
        return Cache::remember($this->cacheKey, $this->cacheDuration, function () {
            try {
                $response = Http::get("https://api.github.com/repos/{$this->githubRepo}/releases/latest");
                
                if ($response->successful()) {
                    $latestRelease = $response->json();
                    $latestVersion = ltrim($latestRelease['tag_name'] ?? '', 'v');
                    
                    if (empty($latestVersion)) {
                        return [
                            'has_update' => false,
                            'message' => 'No version found in latest release',
                            'current_version' => $this->currentVersion
                        ];
                    }
                    
                    $hasUpdate = version_compare($latestVersion, $this->currentVersion, '>');
                    
                    return [
                        'has_update' => $hasUpdate,
                        'current_version' => $this->currentVersion,
                        'latest_version' => $latestVersion,
                        'release_notes' => $latestRelease['body'] ?? 'No release notes available',
                        'download_url' => $latestRelease['zipball_url'] ?? null,
                        'published_at' => $latestRelease['published_at'] ?? null
                    ];
                }
                
                return [
                    'has_update' => false,
                    'message' => 'Failed to check for updates: ' . ($response->json()['message'] ?? 'Unknown error'),
                    'current_version' => $this->currentVersion
                ];
            } catch (Exception $e) {
                Log::error('Update check failed: ' . $e->getMessage());
                
                return [
                    'has_update' => false,
                    'message' => 'Failed to check for updates: ' . $e->getMessage(),
                    'current_version' => $this->currentVersion
                ];
            }
        });
    }
    
    /**
     * clear update cache
     */
    public function clearUpdateCache()
    {
        Cache::forget($this->cacheKey);
    }
    
    /**
     * download update from github
     */
    public function downloadUpdate($downloadUrl)
    {
        try {
            $response = Http::get($downloadUrl);
            
            if ($response->successful()) {
                $zipPath = storage_path('app/update.zip');
                File::put($zipPath, $response->body());
                
                return [
                    'success' => true,
                    'message' => 'Update downloaded successfully',
                    'zip_path' => $zipPath
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Failed to download update: ' . ($response->json()['message'] ?? 'Unknown error')
            ];
        } catch (Exception $e) {
            Log::error('Update download failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to download update: ' . $e->getMessage()
            ];
        }
    }
} 
<?php

namespace App\Console\Commands;

use App\Models\SystemVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CheckForSystemUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:check-updates {--force : Force check even if checked recently}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for system updates from the official repository';

    /**
     * The GitHub API endpoint for releases.
     * 
     * @var string
     */
    protected $githubApiUrl = 'https://api.github.com/repos/pisknk/Barangay-Certifier/releases/latest';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for system updates...');
        
        // Get current version from database
        $currentVersion = SystemVersion::currentVersion();
        $this->info("Current version: {$currentVersion}");
        
        // Check if we should skip due to recent check (unless forced)
        $forceCheck = $this->option('force');
        $lastCheckFile = storage_path('app/system/last_update_check.txt');
        
        if (!$forceCheck && File::exists($lastCheckFile)) {
            $lastCheck = File::get($lastCheckFile);
            $lastCheckTime = (int) $lastCheck;
            $hoursSinceLastCheck = (time() - $lastCheckTime) / 3600;
            
            // If we checked in the last 24 hours and not forcing, skip
            if ($hoursSinceLastCheck < 24) {
                $this->info("Last check was only {$hoursSinceLastCheck} hours ago. Use --force to check anyway.");
                return 0;
            }
        }
        
        try {
            // Make a request to GitHub API to get the latest release
            $response = Http::get($this->githubApiUrl);
            
            if ($response->successful()) {
                $release = $response->json();
                $latestVersion = ltrim($release['tag_name'], 'v');
                $releaseName = $release['name'] ?? 'Unnamed Release';
                $releaseNotes = $release['body'] ?? 'No release notes available';
                $releaseDate = $release['published_at'] ?? now();
                
                // Update the last check time
                File::ensureDirectoryExists(storage_path('app/system'));
                File::put($lastCheckFile, time());
                
                // Compare versions
                if (version_compare($latestVersion, $currentVersion, '>')) {
                    $this->info("New version available: {$latestVersion} ({$releaseName})");
                    $this->info("Release notes: " . substr($releaseNotes, 0, 200) . (strlen($releaseNotes) > 200 ? '...' : ''));
                    
                    // Create a system_updates file with the details
                    $updateInfo = [
                        'current_version' => $currentVersion,
                        'latest_version' => $latestVersion,
                        'version_name' => $releaseName,
                        'release_notes' => $releaseNotes,
                        'release_date' => $releaseDate,
                        'check_time' => time(),
                        'download_url' => $release['zipball_url'] ?? null,
                    ];
                    
                    File::put(
                        storage_path('app/system/update_available.json'),
                        json_encode($updateInfo, JSON_PRETTY_PRINT)
                    );
                    
                    return 1; // Update available
                } else {
                    $this->info("Your system is up to date!");
                    // Remove update file if exists
                    if (File::exists(storage_path('app/system/update_available.json'))) {
                        File::delete(storage_path('app/system/update_available.json'));
                    }
                    return 0; // No update
                }
            } else {
                $this->error("Failed to check for updates: " . $response->status());
                Log::error("Failed to check for updates: " . $response->body());
                return 2; // Error
            }
        } catch (\Exception $e) {
            $this->error("Error checking for updates: " . $e->getMessage());
            Log::error("Error checking for updates: " . $e->getMessage());
            return 2; // Error
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\SystemVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;
use ZipArchive;

class UpdateSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update {--force : Force update even if no update is available}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the system to the latest version';

    /**
     * The backup path.
     * 
     * @var string
     */
    protected $backupPath;

    /**
     * The temporary download path.
     * 
     * @var string
     */
    protected $tempPath;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting system update process...');
        
        // Setup paths
        $this->backupPath = storage_path('app/system/backup_' . time());
        $this->tempPath = storage_path('app/system/temp_' . time());

        // Check if an update is available
        $updateFile = storage_path('app/system/update_available.json');
        
        if (!File::exists($updateFile) && !$this->option('force')) {
            $this->info('Checking for updates first...');
            $exitCode = Artisan::call('system:check-updates', ['--force' => true]);
            
            if ($exitCode === 0) {
                $this->info('No updates available.');
                return 0;
            } else if ($exitCode === 2) {
                $this->error('Failed to check for updates.');
                return 1;
            }
        }
        
        if (!File::exists($updateFile) && !$this->option('force')) {
            $this->error('No update information available.');
            return 1;
        }
        
        // Read update info if available
        if (File::exists($updateFile)) {
            $updateInfo = json_decode(File::get($updateFile), true);
            $this->info("Preparing to update from version {$updateInfo['current_version']} to {$updateInfo['latest_version']}");
            
            if (!$this->confirm('Do you want to continue with the update?', true)) {
                $this->info('Update cancelled.');
                return 0;
            }
            
            $downloadUrl = $updateInfo['download_url'] ?? null;
        } else {
            // If forcing, get latest release directly
            $this->info('Forcing update - getting latest release info...');
            try {
                $response = Http::get('https://api.github.com/repos/pisknk/Barangay-Certifier/releases/latest');
                if (!$response->successful()) {
                    $this->error('Failed to get latest release information.');
                    return 1;
                }
                
                $release = $response->json();
                $downloadUrl = $release['zipball_url'] ?? null;
                $latestVersion = ltrim($release['tag_name'], 'v');
                
                if (empty($downloadUrl)) {
                    $this->error('No download URL found for latest release.');
                    return 1;
                }
                
                $this->info("Found latest version: {$latestVersion}");
            } catch (\Exception $e) {
                $this->error('Error getting latest release: ' . $e->getMessage());
                Log::error('Error getting latest release: ' . $e->getMessage());
                return 1;
            }
        }
        
        // Perform the update
        try {
            // 1. Create backup
            if (!$this->createBackup()) {
                $this->error('Failed to create backup. Aborting update.');
                return 1;
            }
            
            // 2. Download update
            if (!$this->downloadUpdate($downloadUrl)) {
                $this->error('Failed to download update. Aborting.');
                return 1;
            }
            
            // 3. Extract update
            if (!$this->extractUpdate()) {
                $this->error('Failed to extract update. Aborting.');
                return 1;
            }
            
            // 4. Apply update
            if (!$this->applyUpdate()) {
                $this->error('Failed to apply update. Attempting to restore from backup...');
                $this->restoreFromBackup();
                return 1;
            }
            
            // 5. Run migrations and clear caches
            if (!$this->finalize()) {
                $this->error('Failed to finalize update. System may be in an inconsistent state.');
                return 1;
            }
            
            // 6. Update database version record
            $this->updateVersionRecord($updateInfo['latest_version'] ?? $latestVersion ?? '1.0.0');
            
            // 7. Clean up
            $this->cleanup();
            
            $this->info('System update completed successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error during update process: ' . $e->getMessage());
            Log::error('Error during update process: ' . $e->getMessage());
            $this->warn('Attempting to restore from backup...');
            $this->restoreFromBackup();
            return 1;
        }
    }
    
    /**
     * Create a backup of the current system.
     *
     * @return bool
     */
    protected function createBackup()
    {
        $this->info('Creating system backup...');
        
        try {
            // Create backup directory
            File::ensureDirectoryExists($this->backupPath);
            
            // Copy important directories
            $directories = ['app', 'config', 'database', 'resources', 'routes'];
            foreach ($directories as $dir) {
                $this->info("Backing up {$dir} directory...");
                File::copyDirectory(base_path($dir), "{$this->backupPath}/{$dir}");
            }
            
            // Copy important files
            $files = ['.env', 'composer.json', 'composer.lock', 'artisan'];
            foreach ($files as $file) {
                if (File::exists(base_path($file))) {
                    File::copy(base_path($file), "{$this->backupPath}/{$file}");
                }
            }
            
            $this->info('Backup created successfully.');
            return true;
        } catch (\Exception $e) {
            $this->error('Failed to create backup: ' . $e->getMessage());
            Log::error('Failed to create backup: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Download the update package.
     *
     * @param string $url
     * @return bool
     */
    protected function downloadUpdate($url)
    {
        $this->info('Downloading update package...');
        
        try {
            // Create temp directory
            File::ensureDirectoryExists($this->tempPath);
            
            // Download the file
            $zipFile = "{$this->tempPath}/update.zip";
            $response = Http::withOptions(['sink' => $zipFile])->get($url);
            
            if (!$response->successful() || !File::exists($zipFile)) {
                $this->error('Failed to download update package.');
                return false;
            }
            
            $this->info('Update package downloaded successfully.');
            return true;
        } catch (\Exception $e) {
            $this->error('Error downloading update: ' . $e->getMessage());
            Log::error('Error downloading update: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Extract the update package.
     *
     * @return bool
     */
    protected function extractUpdate()
    {
        $this->info('Extracting update package...');
        
        try {
            $zipFile = "{$this->tempPath}/update.zip";
            $extractPath = "{$this->tempPath}/extracted";
            
            // Create extraction directory
            File::ensureDirectoryExists($extractPath);
            
            // Extract zip file
            $zip = new ZipArchive;
            if ($zip->open($zipFile) !== true) {
                $this->error('Failed to open the update package.');
                return false;
            }
            
            $zip->extractTo($extractPath);
            $zip->close();
            
            // GitHub API downloads contain a root folder with the repository name
            // We need to find that folder and use its contents
            $rootFolder = null;
            foreach (File::directories($extractPath) as $directory) {
                $rootFolder = $directory;
                break;
            }
            
            if (!$rootFolder) {
                $this->error('Could not find extracted files.');
                return false;
            }
            
            $this->info('Update package extracted successfully.');
            return true;
        } catch (\Exception $e) {
            $this->error('Error extracting update: ' . $e->getMessage());
            Log::error('Error extracting update: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Apply the update.
     *
     * @return bool
     */
    protected function applyUpdate()
    {
        $this->info('Applying update...');
        
        try {
            $extractPath = "{$this->tempPath}/extracted";
            
            // Find the extracted root folder
            $rootFolder = null;
            foreach (File::directories($extractPath) as $directory) {
                $rootFolder = $directory;
                break;
            }
            
            if (!$rootFolder) {
                $this->error('Could not find extracted files.');
                return false;
            }
            
            // Copy directories to update
            $directories = ['app', 'config', 'database', 'resources', 'routes'];
            foreach ($directories as $dir) {
                if (File::isDirectory("{$rootFolder}/{$dir}")) {
                    $this->info("Updating {$dir} directory...");
                    File::copyDirectory("{$rootFolder}/{$dir}", base_path($dir));
                }
            }
            
            // Copy files to update
            $files = ['composer.json', 'composer.lock', 'artisan'];
            foreach ($files as $file) {
                if (File::exists("{$rootFolder}/{$file}")) {
                    $this->info("Updating {$file}...");
                    File::copy("{$rootFolder}/{$file}", base_path($file));
                }
            }
            
            // Don't override .env file
            
            $this->info('Update applied successfully.');
            return true;
        } catch (\Exception $e) {
            $this->error('Error applying update: ' . $e->getMessage());
            Log::error('Error applying update: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Finalize the update by running migrations and clearing caches.
     *
     * @return bool
     */
    protected function finalize()
    {
        $this->info('Finalizing update...');
        
        try {
            // Run composer install
            $this->info('Installing dependencies...');
            $process = Process::timeout(300)->run('cd ' . base_path() . ' && composer install --no-dev --optimize-autoloader');
            if (!$process->successful()) {
                $this->error('Failed to install dependencies: ' . $process->errorOutput());
                return false;
            }
            
            // Clear caches
            $this->info('Clearing caches...');
            Artisan::call('optimize:clear');
            
            // Run migrations
            $this->info('Running database migrations...');
            Artisan::call('migrate', ['--force' => true]);
            
            $this->info('Update finalized successfully.');
            return true;
        } catch (\Exception $e) {
            $this->error('Error finalizing update: ' . $e->getMessage());
            Log::error('Error finalizing update: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update the version record in the database.
     *
     * @param string $version
     * @return void
     */
    protected function updateVersionRecord($version)
    {
        $this->info('Updating version record...');
        
        try {
            SystemVersion::create([
                'version_number' => $version,
                'version_name' => 'Updated Release',
                'release_notes' => 'System updated to version ' . $version,
                'release_date' => now(),
                'is_critical_update' => false,
            ]);
            
            $this->info('Version record updated successfully.');
        } catch (\Exception $e) {
            $this->error('Error updating version record: ' . $e->getMessage());
            Log::error('Error updating version record: ' . $e->getMessage());
        }
    }
    
    /**
     * Restore the system from backup.
     *
     * @return bool
     */
    protected function restoreFromBackup()
    {
        $this->info('Restoring system from backup...');
        
        try {
            if (!File::isDirectory($this->backupPath)) {
                $this->error('Backup directory not found.');
                return false;
            }
            
            // Restore directories
            $directories = ['app', 'config', 'database', 'resources', 'routes'];
            foreach ($directories as $dir) {
                if (File::isDirectory("{$this->backupPath}/{$dir}")) {
                    $this->info("Restoring {$dir} directory...");
                    File::deleteDirectory(base_path($dir));
                    File::copyDirectory("{$this->backupPath}/{$dir}", base_path($dir));
                }
            }
            
            // Restore files
            $files = ['composer.json', 'composer.lock', 'artisan'];
            foreach ($files as $file) {
                if (File::exists("{$this->backupPath}/{$file}")) {
                    $this->info("Restoring {$file}...");
                    File::copy("{$this->backupPath}/{$file}", base_path($file));
                }
            }
            
            // Don't restore .env file to avoid overwriting database credentials
            
            $this->info('System restored from backup.');
            return true;
        } catch (\Exception $e) {
            $this->error('Error restoring from backup: ' . $e->getMessage());
            Log::error('Error restoring from backup: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clean up temporary files.
     *
     * @return void
     */
    protected function cleanup()
    {
        $this->info('Cleaning up...');
        
        try {
            if (File::isDirectory($this->tempPath)) {
                File::deleteDirectory($this->tempPath);
            }
            
            // Keep the backup for safety
            $this->info('Temporary files cleaned up.');
        } catch (\Exception $e) {
            $this->error('Error during cleanup: ' . $e->getMessage());
            Log::error('Error during cleanup: ' . $e->getMessage());
        }
    }
}

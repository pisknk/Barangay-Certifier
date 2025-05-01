<?php

namespace App\Http\Controllers;

use App\Services\UpdateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Exception;

class UpdateController extends Controller
{
    protected $updateService;
    
    public function __construct(UpdateService $updateService)
    {
        $this->updateService = $updateService;
    }
    
    /**
     * display updates page
     */
    public function index()
    {
        $updateInfo = $this->updateService->checkForUpdates();
        return view('admin.updates', compact('updateInfo'));
    }
    
    /**
     * check for updates (ajax)
     */
    public function checkForUpdates()
    {
        $this->updateService->clearUpdateCache();
        $updateInfo = $this->updateService->checkForUpdates();
        
        return response()->json($updateInfo);
    }
    
    /**
     * download update
     */
    public function downloadUpdate(Request $request)
    {
        $updateInfo = $this->updateService->checkForUpdates();
        
        if (!$updateInfo['has_update']) {
            return redirect()->route('admin.updates')
                ->with('error', 'No updates available to download.');
        }
        
        $result = $this->updateService->downloadUpdate($updateInfo['download_url']);
        
        if (!$result['success']) {
            return redirect()->route('admin.updates')
                ->with('error', $result['message']);
        }
        
        return redirect()->route('admin.updates')
            ->with('success', 'Update downloaded successfully. Ready to install.');
    }
    
    /**
     * install update
     */
    public function installUpdate()
    {
        try {
            $zipPath = storage_path('app/update.zip');
            
            if (!File::exists($zipPath)) {
                return redirect()->route('admin.updates')
                    ->with('error', 'Update file not found. Please download the update first.');
            }
            
            // create backup
            $backupPath = storage_path('app/backup_' . date('YmdHis') . '.zip');
            $this->createBackup($backupPath);
            
            // extract the update
            $extractPath = storage_path('app/update_extract');
            
            // ensure extract directory exists and is empty
            if (File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
            
            File::makeDirectory($extractPath, 0755, true);
            
            // extract zip
            $zip = new ZipArchive;
            if ($zip->open($zipPath) === true) {
                $zip->extractTo($extractPath);
                $zip->close();
                
                // get the subfolder name (repo-branch format from GitHub)
                $folders = File::directories($extractPath);
                if (empty($folders)) {
                    throw new Exception("No folders found in the update package");
                }
                
                $updateSourcePath = $folders[0]; // get first directory
                
                // copy files to main app
                $this->copyFiles($updateSourcePath, base_path());
                
                // update version file
                $updateInfo = $this->updateService->checkForUpdates();
                $versionData = [
                    'version' => $updateInfo['latest_version'],
                    'build' => date('YmdHis'),
                    'updated_at' => now()->toDateTimeString()
                ];
                
                File::put(base_path('version.json'), json_encode($versionData, JSON_PRETTY_PRINT));
                
                // clean up
                File::delete($zipPath);
                File::deleteDirectory($extractPath);
                
                // clear update cache
                $this->updateService->clearUpdateCache();
                
                return redirect()->route('admin.updates')
                    ->with('success', 'Update installed successfully. New version: ' . $updateInfo['latest_version']);
            } else {
                throw new Exception("Failed to open the update archive");
            }
        } catch (Exception $e) {
            Log::error('Update installation failed: ' . $e->getMessage());
            
            return redirect()->route('admin.updates')
                ->with('error', 'Update installation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * create backup of the current app
     */
    protected function createBackup($backupPath)
    {
        try {
            $zip = new ZipArchive;
            
            if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(base_path()),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                
                $excludeDirs = [
                    'vendor',
                    'node_modules',
                    'storage/app',
                    'storage/logs',
                    'storage/framework',
                    '.git',
                ];
                
                foreach ($files as $name => $file) {
                    if (!$file->isDir()) {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen(base_path()) + 1);
                        
                        // check if file should be excluded
                        $exclude = false;
                        foreach ($excludeDirs as $excludeDir) {
                            if (strpos($relativePath, $excludeDir) === 0) {
                                $exclude = true;
                                break;
                            }
                        }
                        
                        if (!$exclude) {
                            $zip->addFile($filePath, $relativePath);
                        }
                    }
                }
                
                $zip->close();
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('Backup creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * copy files from extracted update to main app
     */
    protected function copyFiles($source, $destination)
    {
        $excludeDirs = [
            'vendor',
            'node_modules',
            'storage/app',
            'storage/logs',
            'storage/framework',
            '.git',
        ];
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $relativePath = substr($item->getPathname(), strlen($source) + 1);
            $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;
            
            // check if file should be excluded
            $exclude = false;
            foreach ($excludeDirs as $excludeDir) {
                if (strpos($relativePath, $excludeDir) === 0) {
                    $exclude = true;
                    break;
                }
            }
            
            if ($exclude) {
                continue;
            }
            
            if ($item->isDir()) {
                if (!File::exists($targetPath)) {
                    File::makeDirectory($targetPath, 0755, true);
                }
            } else {
                File::copy($item->getPathname(), $targetPath);
            }
        }
    }
} 
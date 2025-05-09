<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

class TenantSettingsController extends Controller
{
    /**
     * Display the settings page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get the current authenticated user
        $user = Auth::guard('tenant')->user();
        
        // Try to get user settings from database or create with defaults
        $settings = TenantSetting::firstOrNew(['tenant_user_id' => $user->id]);
        
        // Set default values if not already set
        if (!$settings->exists) {
            $settings->barangay_logo = '/assets/img/default-barangay-logo.png';
            $settings->municipality_logo = '/assets/img/default-municipality-logo.png';
            $settings->header = 'Republic of the Philippines\nProvince of Sample Province\nMunicipality of Sample Municipality\nBarangay Sample';
            $settings->province = 'Sample Province';
            $settings->municipality = 'Sample Municipality';
            $settings->municipality_type = 'Municipality';
            $settings->punong_barangay = 'HON. HALIM T. DIMACANGAN';
            $settings->paper_size = 'A4';
            $settings->theme = 'light';
        }
        
        // Get tenant for subscription details
        $tenant = tenant();
        
        // Prepare settings array for the view
        $viewSettings = [
            'barangay_logo' => $settings->barangay_logo,
            'municipality_logo' => $settings->municipality_logo,
            'header' => $settings->header,
            'province' => $settings->province,
            'municipality' => $settings->municipality,
            'municipality_type' => $settings->municipality_type,
            'punong_barangay' => $settings->punong_barangay,
            'paper_size' => $settings->paper_size,
            'theme' => $settings->theme,
            
            // Subscription-related information
            'subscription_plan' => $tenant->subscription_plan ?? 'Basic P399',
            'subscription_expiry' => $tenant->valid_until ? $tenant->valid_until->format('F d, Y') : 'Not set',
            'subscription_owner' => $tenant->name,
            'subscription_purchased_at' => $tenant->created_at ? $tenant->created_at->format('F d, Y') : 'Not available',
        ];
        
        return view('tenant.settings.index', compact('viewSettings'));
    }
    
    /**
     * Update certificate settings
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCertificateSettings(Request $request)
    {
        // Get the current authenticated user
        $user = Auth::guard('tenant')->user();
        
        // Find or create settings for this user
        $settings = TenantSetting::firstOrNew(['tenant_user_id' => $user->id]);
        
        // Handle logo uploads or URL settings
        if ($request->hasFile('barangay_logo')) {
            $path = $request->file('barangay_logo')->store('logos', 'tenant');
            $settings->barangay_logo = Storage::disk('tenant')->url($path);
        } elseif ($request->filled('barangay_logo_url')) {
            $settings->barangay_logo = $request->barangay_logo_url;
        }
        
        if ($request->hasFile('municipality_logo')) {
            $path = $request->file('municipality_logo')->store('logos', 'tenant');
            $settings->municipality_logo = Storage::disk('tenant')->url($path);
        } elseif ($request->filled('municipality_logo_url')) {
            $settings->municipality_logo = $request->municipality_logo_url;
        }
        
        // Get tenant for subscription plan check
        $tenant = tenant();
        $plan = $tenant->subscription_plan ?? 'Basic P399';
        
        // Check if paper size customization is allowed for this plan - using case-insensitive matching
        $planLower = strtolower($plan);
        $canCustomizePaperSize = (stripos($planLower, 'essentials') !== false || stripos($planLower, 'ultimate') !== false);
        
        // Update paper size only if the feature is available
        if ($canCustomizePaperSize) {
            $settings->paper_size = $request->paper_size;
        } else if ($request->paper_size != 'A4' && $settings->exists && $settings->paper_size != 'A4') {
            // Reset to A4 if they're trying to change it and don't have the feature
            $settings->paper_size = 'A4';
        }
        
        // Update certificate header text and other settings (available to all plans)
        $settings->province = $request->province;
        $settings->municipality = $request->municipality;
        $settings->municipality_type = $request->municipality_type;
        $settings->punong_barangay = $request->punong_barangay;
        
        // Generate and save header
        $header = "Republic of the Philippines\n";
        $header .= "Province of " . $request->province . "\n";
        $header .= $request->municipality_type . " of " . $request->municipality . "\n";
        $header .= "Barangay " . tenant()->barangay;
        $settings->header = $header;
        
        // Save settings
        $settings->save();
        
        return redirect()->route('tenant.settings.index')->with('success', 'Certificate settings updated successfully.');
    }
    
    /**
     * Update website settings
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateWebsiteSettings(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,auto',
        ]);
        
        // Get the current authenticated user
        $user = Auth::guard('tenant')->user();
        
        // Find or create settings for this user
        $settings = TenantSetting::firstOrNew(['tenant_user_id' => $user->id]);
        
        // Update theme
        $settings->theme = $request->theme;
        
        // Save settings
        $settings->save();
        
        return redirect()->route('tenant.settings.index')->with('success', 'Website settings updated successfully.');
    }
    
    /**
     * Check for software updates
     * 
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function checkForUpdates(Request $request)
    {
        // Get the current authenticated user and tenant
        $user = Auth::guard('tenant')->user();
        $tenant = tenant();
        
        // Check if user has permission to check for updates (Ultimate plan only)
        $tenantPlan = $tenant->subscription_plan ?? 'Basic P399';
        $canGetSoftwareUpdates = (stripos(strtolower($tenantPlan), 'ultimate') !== false);
        
        if (!$canGetSoftwareUpdates) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Software updates are only available on the Ultimate plan.'
                ], 403);
            }
            
            return redirect()->route('tenant.settings.index')
                ->with('error', 'Software updates are only available on the Ultimate plan.');
        }
        
        try {
            // Run the artisan command to check for updates with force flag
            $exitCode = Artisan::call('system:check-updates', ['--force' => true]);
            
            // Check exit code to determine the result
            if ($exitCode === 1) {
                // Update available
                $updateFile = storage_path('app/system/update_available.json');
                if (file_exists($updateFile)) {
                    $updateInfo = json_decode(file_get_contents($updateFile), true);
                    $message = 'New version available: ' . $updateInfo['latest_version'] . 
                               ' (' . $updateInfo['version_name'] . ')';
                    
                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => 'success',
                            'message' => $message,
                            'update_available' => true,
                            'update_info' => $updateInfo
                        ]);
                    }
                    
                    return redirect()->route('tenant.settings.index')
                        ->with('info', $message);
                }
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'A new version is available!',
                        'update_available' => true
                    ]);
                }
                
                return redirect()->route('tenant.settings.index')
                    ->with('info', 'A new version is available!');
            } elseif ($exitCode === 0) {
                // No updates available
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Software is up to date!',
                        'update_available' => false
                    ]);
                }
                
                return redirect()->route('tenant.settings.index')
                    ->with('success', 'Software is up to date!');
            } else {
                // Error checking for updates
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to check for updates. Please try again later.'
                    ]);
                }
                
                return redirect()->route('tenant.settings.index')
                    ->with('error', 'Failed to check for updates. Please try again later.');
            }
        } catch (\Exception $e) {
            Log::error('Error checking for updates: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while checking for updates: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->route('tenant.settings.index')
                ->with('error', 'An error occurred while checking for updates: ' . $e->getMessage());
        }
    }
    
    /**
     * Save theme settings via AJAX
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveThemeSettings(Request $request)
    {
        // Get the current authenticated user
        $user = Auth::guard('tenant')->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // Get current tenant
        $tenant = tenant();
        if (!$tenant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant not found'
            ], 404);
        }
        
        // Check subscription plan permission
        $tenantPlan = $tenant->subscription_plan ?? 'Basic P399';
        $canCustomizeTheme = false;
        
        // Check for "Ultimate" in the plan name (case insensitive)
        if (stripos($tenantPlan, 'ultimate') !== false) {
            $canCustomizeTheme = true;
        }
        
        // Log the permission check
        \Illuminate\Support\Facades\Log::info('Theme save permission check', [
            'user_id' => $user->id,
            'tenant_plan' => $tenantPlan,
            'can_customize' => $canCustomizeTheme
        ]);
        
        // If user doesn't have permission, return error
        if (!$canCustomizeTheme) {
            return response()->json([
                'status' => 'error',
                'message' => 'Theme customization is not available on your current subscription plan'
            ], 403);
        }
        
        // Get settings as plain array
        $themeData = $request->all();
        
        // Add user ID to settings to ensure they're user-specific
        $themeData['user_id'] = $user->id;
        
        // Convert to JSON string for storage
        $jsonData = json_encode($themeData);
        
        try {
            // Store directly in database column - using user ID as the key
            DB::connection('tenant')
                ->table('tenant_settings')
                ->updateOrInsert(
                    ['tenant_user_id' => $user->id],
                    ['theme_settings' => $jsonData, 'updated_at' => now()]
                );
            
            // Store in session with user-specific key
            $request->session()->put('user_'.$user->id.'_theme_settings', $jsonData);
            
            // Log the action
            \Illuminate\Support\Facades\Log::info('Theme settings saved for specific user', [
                'user_id' => $user->id,
                'theme_data' => $themeData
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Theme settings saved successfully for your account'
            ]);
            
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Error saving theme settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save theme settings: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Perform system update
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function performUpdate(Request $request)
    {
        // Get the current authenticated user and tenant
        $user = Auth::guard('tenant')->user();
        $tenant = tenant();
        
        // Check if user has permission to get updates (Ultimate plan only)
        $tenantPlan = $tenant->subscription_plan ?? 'Basic P399';
        $canGetSoftwareUpdates = (stripos(strtolower($tenantPlan), 'ultimate') !== false);
        
        if (!$canGetSoftwareUpdates) {
            return response()->json([
                'status' => 'error',
                'message' => 'Software updates are only available on the Ultimate plan.'
            ], 403);
        }
        
        // Check if update is available
        $updateFile = storage_path('app/system/update_available.json');
        if (!file_exists($updateFile)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No update information available. Please check for updates first.'
            ], 400);
        }
        
        try {
            // Read update info
            $updateInfo = json_decode(file_get_contents($updateFile), true);
            $currentVersion = $updateInfo['current_version'] ?? 'Unknown';
            $latestVersion = $updateInfo['latest_version'] ?? 'Unknown';
            
            // Start the update process in the background
            $process = Process::start('cd ' . base_path() . ' && php artisan system:update --force > '.storage_path('logs/update.log').' 2>&1');
            
            // Return initial response
            return response()->json([
                'status' => 'success',
                'message' => "Update process started. Updating from version {$currentVersion} to {$latestVersion}.",
                'current_version' => $currentVersion,
                'latest_version' => $latestVersion,
                'process_id' => $process->id(),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error starting update process: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while starting the update process: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get update progress
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpdateProgress(Request $request)
    {
        // Get the log file
        $logFile = storage_path('logs/update.log');
        
        if (!file_exists($logFile)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Update log file not found',
                'progress' => 0,
                'logs' => []
            ]);
        }
        
        try {
            // Read the log file
            $logs = file_get_contents($logFile);
            $logLines = array_filter(explode("\n", $logs));
            
            // Calculate progress based on log content
            $progress = 0;
            $totalSteps = 7; // backup, download, extract, apply, finalize, update version, cleanup
            
            if (strpos($logs, 'Creating system backup') !== false) $progress = max($progress, 1);
            if (strpos($logs, 'Downloading update package') !== false) $progress = max($progress, 2);
            if (strpos($logs, 'Extracting update package') !== false) $progress = max($progress, 3);
            if (strpos($logs, 'Applying update') !== false) $progress = max($progress, 4);
            if (strpos($logs, 'Finalizing update') !== false) $progress = max($progress, 5);
            if (strpos($logs, 'Updating version record') !== false) $progress = max($progress, 6);
            if (strpos($logs, 'Cleaning up') !== false) $progress = max($progress, 7);
            if (strpos($logs, 'System update completed successfully') !== false) $progress = $totalSteps;
            
            $percentProgress = ($progress / $totalSteps) * 100;
            
            return response()->json([
                'status' => 'success',
                'progress' => round($percentProgress),
                'logs' => $logLines,
                'completed' => $progress >= $totalSteps || strpos($logs, 'System update completed successfully') !== false,
                'error' => strpos($logs, 'Error') !== false,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error reading update progress: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while reading update progress: ' . $e->getMessage(),
                'progress' => 0,
                'logs' => []
            ], 500);
        }
    }

    /**
     * Get theme settings via AJAX
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThemeSettings(Request $request)
    {
        // Get the current authenticated user
        $user = Auth::guard('tenant')->user();
        
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated'
            ], 401);
        }
        
        // Get current tenant
        $tenant = tenant();
        if (!$tenant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tenant not found'
            ], 404);
        }
        
        // Check subscription plan permission
        $tenantPlan = $tenant->subscription_plan ?? 'Basic P399';
        $canCustomizeTheme = false;
        
        // Check for "Ultimate" in the plan name (case insensitive)
        if (stripos($tenantPlan, 'ultimate') !== false) {
            $canCustomizeTheme = true;
        }
        
        // Log the permission check
        \Illuminate\Support\Facades\Log::info('Theme get permission check', [
            'user_id' => $user->id,
            'tenant_plan' => $tenantPlan,
            'can_customize' => $canCustomizeTheme
        ]);
        
        // If user doesn't have permission, return error
        if (!$canCustomizeTheme) {
            return response()->json([
                'status' => 'error',
                'message' => 'Theme customization is not available on your current subscription plan'
            ], 403);
        }
        
        try {
            // First try to get settings from session for faster access - with user-specific key
            $sessionSettings = $request->session()->get('user_'.$user->id.'_theme_settings');
            if ($sessionSettings) {
                return response()->json([
                    'status' => 'success',
                    'theme_settings' => $sessionSettings,
                    'source' => 'session',
                    'user_id' => $user->id
                ]);
            }
            
            // If not in session, get from database - using user-specific query
            $dbSettings = DB::connection('tenant')
                ->table('tenant_settings')
                ->where('tenant_user_id', $user->id)
                ->value('theme_settings');
            
            if ($dbSettings) {
                // Store in session for future requests - with user-specific key
                $request->session()->put('user_'.$user->id.'_theme_settings', $dbSettings);
                
                return response()->json([
                    'status' => 'success',
                    'theme_settings' => $dbSettings,
                    'source' => 'database',
                    'user_id' => $user->id
                ]);
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'No theme settings found for this user'
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Error getting theme settings', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve theme settings: ' . $e->getMessage()
            ], 500);
        }
    }
} 
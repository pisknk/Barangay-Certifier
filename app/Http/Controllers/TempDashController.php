<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class TempDashController extends Controller
{
    public function index()
    {
        // Check if the view exists
        if (View::exists('admin.admindash')) {
            // Try to render the admin dashboard view
            try {
                return view('admin.admindash', [
                    'activeTenants' => 5,
                    'totalIncome' => 2000,
                    'totalRevenue' => 5000
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Error rendering view',
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ], 500);
            }
        } else {
            return response()->json([
                'error' => 'View not found',
                'searched_for' => 'admin.admindash',
                'available_views' => $this->getAvailableViews()
            ], 404);
        }
    }
    
    private function getAvailableViews()
    {
        $viewPaths = config('view.paths');
        $views = [];
        
        foreach ($viewPaths as $path) {
            if (is_dir($path)) {
                $views = array_merge($views, $this->scanDir($path));
            }
        }
        
        return $views;
    }
    
    private function scanDir($dir, $prefix = '')
    {
        $results = [];
        $files = scandir($dir);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $path = $dir . '/' . $file;
            $relativePath = $prefix ? $prefix . '.' . $file : $file;
            
            if (is_dir($path)) {
                $results = array_merge($results, $this->scanDir($path, $relativePath));
            } else {
                // Remove .blade.php extension
                $viewName = str_replace('.blade.php', '', $relativePath);
                $results[] = $viewName;
            }
        }
        
        return $results;
    }
} 
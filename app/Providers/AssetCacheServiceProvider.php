<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class AssetCacheServiceProvider extends ServiceProvider
{
    /**
     * register the service provider
     *
     * @return void
     */
    public function register()
    {
        // register services here
    }

    /**
     * bootstrap the service provider
     *
     * @return void
     */
    public function boot()
    {
        // define a custom route for cached telegram emojis
        Route::get('/cached-emoji/{filename}', function (Request $request, $filename) {
            $cacheKey = 'telegram_emoji_' . $filename;
            
            return Cache::remember($cacheKey, 86400, function () use ($filename) {
                $path = 'emojis/' . $filename;
                
                if (!Storage::exists($path)) {
                    abort(404);
                }
                
                $file = Storage::get($path);
                $type = Storage::mimeType($path);
                
                $response = Response::make($file, 200);
                $response->header('Content-Type', $type);
                $response->header('Cache-Control', 'public, max-age=31536000'); // 1 year
                $response->header('Pragma', 'cache');
                
                return $response;
            });
        });
        
        // define a custom route for cached background images
        Route::get('/cached-bg/{filename}', function (Request $request, $filename) {
            $cacheKey = 'background_image_' . $filename;
            
            return Cache::remember($cacheKey, 86400, function () use ($filename) {
                $path = 'backgrounds/' . $filename;
                
                if (!Storage::exists($path)) {
                    abort(404);
                }
                
                $file = Storage::get($path);
                $type = Storage::mimeType($path);
                
                $response = Response::make($file, 200);
                $response->header('Content-Type', $type);
                $response->header('Cache-Control', 'public, max-age=31536000'); // 1 year
                $response->header('Pragma', 'cache');
                
                return $response;
            });
        });
    }
} 
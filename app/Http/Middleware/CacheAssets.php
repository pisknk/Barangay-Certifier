<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheAssets
{
    /**
     * asset types that should be cached
     */
    protected $cacheableAssetTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'application/json', // for telegram emoji json configs
    ];

    /**
     * asset path patterns that should be cached
     */
    protected $cacheablePatterns = [
        '/background/',
        '/bg/',
        '/emoji/',
        '/telegram/',
        '/tg-emoji/',
    ];

    /**
     * handle an incoming request
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // only cache get requests
        if (!$request->isMethod('GET')) {
            return $response;
        }
        
        $path = $request->path();
        $shouldCache = false;
        
        // check if the path contains any of our cacheable patterns
        foreach ($this->cacheablePatterns as $pattern) {
            if (stripos($path, $pattern) !== false) {
                $shouldCache = true;
                break;
            }
        }
        
        if (!$shouldCache) {
            return $response;
        }
        
        // check content type
        $contentType = $response->headers->get('Content-Type');
        if ($contentType && in_array($contentType, $this->cacheableAssetTypes)) {
            // set cache headers
            $response->headers->set('Cache-Control', 'public, max-age=31536000'); // 1 year
            $response->headers->set('Pragma', 'cache');
            $response->setEtag(md5($response->getContent()));
            $response->isNotModified($request);
        }
        
        return $response;
    }
} 
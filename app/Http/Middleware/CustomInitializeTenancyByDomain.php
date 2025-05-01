<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain as BaseMiddleware;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;

class CustomInitializeTenancyByDomain extends BaseMiddleware
{
    /** @var Tenancy */
    protected $tenancy;

    /** @var DomainTenantResolver */
    protected $resolver;

    public function __construct(Tenancy $tenancy, DomainTenantResolver $resolver)
    {
        $this->tenancy = $tenancy;
        $this->resolver = $resolver;
    }

    public function handle($request, Closure $next)
    {
        // Get full http host which includes port number if present
        $domain = $request->getHttpHost();
        
        // Log for debugging
        Log::info('CustomInitializeTenancyByDomain: Processing request', [
            'domain' => $domain,
            'port' => $request->getPort(),
            'host' => $request->getHost(),
        ]);
        
        return $this->initializeTenancy(
            $request, $next, $domain
        );
    }
} 
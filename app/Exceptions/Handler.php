<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        
        // Add detailed logging for 404 errors
        $this->renderable(function (NotFoundHttpException $e, $request) {
            Log::error('404 Not Found: ' . $request->fullUrl(), [
                'method' => $request->method(),
                'path' => $request->path(),
                'domain' => $request->getHost(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->header('referer'),
                'route_list' => app('router')->getRoutes()->getRoutesByMethod()['GET'] ?? []
            ]);
        });
    }
} 
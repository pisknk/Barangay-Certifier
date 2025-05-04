<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    /**
     * validate turnstile token
     */
    public function validate(string $token): bool
    {
        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => env('CAPTCHA_SECRET'),
            'response' => $token,
            'remoteip' => request()->ip(),
        ]);

        $result = $response->json();
        
        return $result['success'] ?? false;
    }
} 
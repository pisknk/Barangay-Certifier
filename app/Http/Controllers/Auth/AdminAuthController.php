<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // We'll handle authentication at the route level instead
    }

    /**
     * Display the admin login view.
     */
    public function showLoginForm(): View
    {
        // Debug statement to verify this method is being called
        info('AdminAuthController@showLoginForm called');
        
        // Return the custom admin login view
        return view('admin.auth.adminlogin');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        
        // Redirect to the new console route, which we know renders admin.admindash
        return redirect('http://admin-panel.localhost:8000/console');
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
} 
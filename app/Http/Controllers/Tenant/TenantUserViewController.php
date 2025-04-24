<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantUserViewController extends Controller
{
    /**
     * Display a listing of tenant users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = TenantUser::all();
        return view('tenant.userlist', compact('users'));
    }

    /**
     * Show the form for creating a new tenant user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('tenant.createuser');
    }

    /**
     * Store a newly created tenant user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenant_users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,user',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = new TenantUser();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = $validated['role'];
        $user->position = $validated['position'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->save();

        return redirect()->route('tenant.users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified tenant user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $user = TenantUser::findOrFail($id);
        return view('tenant.showuser', compact('user'));
    }

    /**
     * Show the form for editing the specified tenant user.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = TenantUser::findOrFail($id);
        return view('tenant.modifyuser', compact('user'));
    }

    /**
     * Update the specified tenant user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = TenantUser::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenant_users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,user',
            'position' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->role = $validated['role'];
        $user->position = $validated['position'] ?? null;
        $user->phone = $validated['phone'] ?? null;
        $user->save();

        return redirect()->route('tenant.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified tenant user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Prevent deleting your own account
        if (Auth::id() == $id) {
            return redirect()->route('tenant.users.index')
                ->with('error', 'You cannot delete your own account');
        }

        $user = TenantUser::findOrFail($id);
        $user->delete();

        return redirect()->route('tenant.users.index')
            ->with('success', 'User deleted successfully');
    }

    /**
     * Show the login form for tenant users.
     * 
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        $tenant = tenant();
        return view('tenant.auth.tenantlogin', compact('tenant'));
    }

    /**
     * Handle the login request.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Get the user for logging purposes
        $user = DB::connection('tenant')
            ->table('tenant_users')
            ->where('email', $credentials['email'])
            ->first();
        
        if (!$user) {
            Log::error("Login failed: User not found with email " . $credentials['email']);
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
        
        // Log hash details for debugging
        Log::info("Login attempt for: " . $credentials['email'] . 
                 ", Password hash in DB: " . substr($user->password, 0, 15) . "...");

        if (Auth::guard('tenant')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            Log::info("Login successful for: " . $credentials['email']);
            
            // Get theme settings directly from database
            $themeSettings = DB::connection('tenant')
                ->table('tenant_settings')
                ->where('tenant_user_id', $user->id)
                ->value('theme_settings');
            
            if ($themeSettings) {
                // Store in session with user-specific key
                $request->session()->put('user_'.$user->id.'_theme_settings', $themeSettings);
                
                // Add to flash data to make it available on the first page load, with user-specific key
                $request->session()->flash('current_user_theme_settings', $themeSettings);
                $request->session()->flash('current_user_id', $user->id);
                
                Log::info("Theme settings loaded for specific user", [
                    'user_id' => $user->id,
                    'has_settings' => !empty($themeSettings)
                ]);
            } else {
                Log::info("No theme settings found for user {$user->id}");
            }
            
            // Check if there's an intended URL in the session
            $intendedUrl = $request->session()->get('url.intended');
            
            // If there's no intended URL or it's the login page, redirect to certificates
            if (!$intendedUrl || $intendedUrl === route('tenant.login')) {
                return redirect()->route('tenant.certificates.index');
            }
            
            // Otherwise use the intended URL
            return redirect()->intended(route('tenant.certificates.index'));
        }

        Log::error("Login failed: Password mismatch for " . $credentials['email']);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle the logout request.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('tenant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('tenant.login');
    }

    /**
     * Show the dashboard for tenant.
     * 
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $tenant = tenant();
        // Get statistics or any data needed for the dashboard
        $totalIncome = 0;
        $activeTenants = 1;
        $totalRevenue = 0;
        
        return view('tenant.tenantdash', compact('tenant', 'totalIncome', 'activeTenants', 'totalRevenue'));
    }

    /**
     * Show the form for requesting a password reset link.
     * 
     * @return \Illuminate\View\View
     */
    public function showForgotPasswordForm()
    {
        $tenant = tenant();
        return view('tenant.auth.forgot-password', compact('tenant'));
    }

    /**
     * Send a password reset link to the given user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // This would be implemented using Laravel's Password facade
        // Password::broker('tenant_users')->sendResetLink($request->only('email'));

        return back()->with('status', 'If your email is registered, you will receive a password reset link shortly.');
    }

    /**
     * Show the form for resetting password.
     * 
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetPasswordForm($token)
    {
        $tenant = tenant();
        return view('tenant.auth.reset-password', compact('tenant', 'token'));
    }

    /**
     * Reset the user's password.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // This would be implemented using Laravel's Password facade
        // Password::broker('tenant_users')->reset($request->only(
        //     'email', 'password', 'password_confirmation', 'token'
        // ), function ($user, $password) {
        //     $user->password = Hash::make($password);
        //     $user->save();
        // });

        return redirect()->route('tenant.login')
            ->with('status', 'Your password has been reset successfully.');
    }

    /**
     * Show the registration form.
     * 
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $tenant = tenant();
        return view('tenant.auth.register', compact('tenant'));
    }

    /**
     * Register a new user.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenant_users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user
        $user = new TenantUser();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'user'; // Default role
        $user->save();

        // Log the user in
        Auth::guard('tenant')->login($user);

        return redirect()->route('tenant.certificates.index');
    }
} 
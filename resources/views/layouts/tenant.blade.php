<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(\Illuminate\Support\Facades\Auth::guard('tenant')->check())
      @php $currentUserId = \Illuminate\Support\Facades\Auth::guard('tenant')->id(); @endphp
      <meta name="current-user-id" content="{{ $currentUserId }}">
      @if(session('user_'.$currentUserId.'_theme_settings'))
      <meta name="user-theme-settings" content="{{ session('user_'.$currentUserId.'_theme_settings') }}">
      @elseif(session('current_user_theme_settings') && session('current_user_id') == $currentUserId)
      <meta name="user-theme-settings" content="{{ session('current_user_theme_settings') }}">
      @endif
    @endif
    <link
      rel="apple-touch-icon"
      sizes="76x76"
      href="/assets/img/apple-icon.png"
    />
    <link rel="icon" type="image/png" href="/assets/img/favicon.png" />
    <title>@yield('title', config('app.name', 'Barangay Certifier'))</title>
    
    <!-- Fonts and icons -->
    <link
      rel="stylesheet"
      type="text/css"
      href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900"
    />
    <!-- Nucleo Icons -->
    <link href="/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script
      src="https://kit.fontawesome.com/42d5adcbca.js"
      crossorigin="anonymous"
    ></script>
    <!-- Material Icons -->
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"
    />
    <!-- CSS Files -->
    <link
      id="pagestyle"
      href="/assets/css/material-dashboard.css?v=3.2.0"
      rel="stylesheet"
    />
    <!-- Custom CSS -->
    <link href="/assets/css/custom.css" rel="stylesheet" />

    @stack('styles')
    
    <style>
      /* Custom styles for layout */
      .main-content {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }
      
      .container-fluid {
        flex: 1 0 auto;
        padding-bottom: 60px; /* Space for footer */
      }
      
      .footer {
        flex-shrink: 0;
        margin-top: auto;
      }
      
      @media (max-width: 991.98px) {
        .container-fluid {
          padding-bottom: 70px; /* More space for footer on mobile */
        }
      }
    </style>
  </head>

  <body class="g-sidenav-show bg-gray-100">
    @if(!isset($hideNavbar) || !$hideNavbar)
      @include('tenant.partials.sidebar')
    @endif
    
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
      @yield('content')
    </main>
    
    @php 
      // Only hide config panel for non-Ultimate plans
      $currentTenant = tenant();
      $plan = $currentTenant->subscription_plan ?? 'Basic P399';
      $hideConfigPanel = !(stripos(strtolower($plan), 'ultimate') !== false);
    @endphp
    
    @if(!$hideConfigPanel)
    <div class="fixed-plugin">
      <a class="fixed-plugin-button text-dark position-fixed px-3 py-2" id="theme-settings-button">
        <i class="material-symbols-rounded py-2">settings</i>
      </a>
      <div class="card shadow-lg">
        <div class="card-header pb-0 pt-3">
          <div class="float-start">
            <h5 class="mt-3 mb-0">Theme Customizer</h5>
            <p>See our dashboard options.</p>
          </div>
          <div class="float-end mt-4">
            <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
              <i class="material-symbols-rounded">clear</i>
            </button>
          </div>
        </div>
        <hr class="horizontal dark my-1">
        <div class="card-body pt-sm-3 pt-0">
          <!-- Sidebar Backgrounds -->
          <div>
            <h6 class="mb-0">Sidebar Colors</h6>
          </div>
          <a href="javascript:void(0)" class="switch-trigger background-color">
            <div class="badge-colors my-2 text-start">
              <span class="badge filter bg-gradient-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
              <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
            </div>
          </a>
          <!-- Sidenav Type -->
          <div class="mt-3">
            <h6 class="mb-0">Sidenav Type</h6>
            <p class="text-sm">Choose between different sidenav types.</p>
          </div>
          <div class="d-flex">
            <button class="btn bg-gradient-dark px-3 mb-2 active" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>
            <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
            <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
          </div>
          <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
          <!-- Navbar Fixed -->
          <div class="mt-3 d-flex">
            <h6 class="mb-0">Navbar Fixed</h6>
            <div class="form-check form-switch ps-0 ms-auto my-auto">
              <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
            </div>
          </div>
          <hr class="horizontal dark my-3">
          <div class="mt-2 d-flex">
            <h6 class="mb-0">Light / Dark</h6>
            <div class="form-check form-switch ps-0 ms-auto my-auto">
              <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
            </div>
          </div>
          <hr class="horizontal dark my-sm-4">
          
          <div class="text-center">
            <button id="save-theme-settings" class="btn bg-gradient-primary w-100">
              <i class="material-symbols-rounded me-2">save</i> Save Theme Settings
            </button>
            <div id="save-status" class="text-sm text-success mt-2" style="display: none;">
              Settings saved successfully!
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- Core JS Files -->
    <script src="/assets/js/core/popper.min.js"></script>
    <script src="/assets/js/core/bootstrap.min.js"></script>
    <script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="/assets/js/plugins/chartjs.min.js"></script>
    
    <script>
      // Get subscription plan information
      function getSubscriptionPlan() {
        @php
          $currentTenant = tenant();
          $plan = $currentTenant->subscription_plan ?? 'Basic P399';
          echo "return '$plan';";
        @endphp
      }
      
      // Check if the current plan allows theme customization
      function canCustomizeTheme() {
        const plan = getSubscriptionPlan().toLowerCase();
        console.log('Current subscription plan (lowercase):', plan);
        return plan.includes('ultimate');
      }
      
      var win = navigator.platform.indexOf("Win") > -1;
      if (win && document.querySelector("#sidenav-scrollbar")) {
        var options = {
          damping: "0.5",
        };
        Scrollbar.init(document.querySelector("#sidenav-scrollbar"), options);
      }
    </script>
    
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="/assets/js/material-dashboard.min.js?v=3.2.0"></script>
    
    <script>
      // Initialize theme settings on page load
      document.addEventListener('DOMContentLoaded', function() {
        // Apply default theme first (light theme)
        applyDefaultTheme();
        
        // Then try to fetch from server (if user is logged in)
        fetchServerThemeSettings().then(success => {
          console.log('Fetched theme settings successfully:', success);
        });
        
        // Add event listener for the save button
        const saveButton = document.getElementById('save-theme-settings');
        if (saveButton) {
          saveButton.addEventListener('click', function() {
            // Check if theme customization is allowed
            if (!canCustomizeTheme()) {
              alert('Theme customization is only available on the Ultimate plan.');
              return;
            }
            
            saveThemeSettings(true);
          });
        }
      });
      
      // Track if settings have been changed since last save
      let settingsChanged = false;
      
      // Get the current user ID if authenticated
      function getCurrentUserId() {
        const userIdMeta = document.querySelector('meta[name="current-user-id"]');
        return userIdMeta ? userIdMeta.getAttribute('content') : null;
      }
      
      // Get localStorage key for current user
      function getLocalStorageKey() {
        const userId = getCurrentUserId();
        return userId ? `themeSettings_user_${userId}` : 'themeSettings_guest';
      }
      
      // Apply default theme settings
      function applyDefaultTheme() {
        // Check if we have theme settings in meta tag (from session)
        const metaThemeSettings = document.querySelector('meta[name="user-theme-settings"]');
        if (metaThemeSettings) {
          try {
            const settings = JSON.parse(metaThemeSettings.getAttribute('content'));
            console.log('Applying theme settings from meta tag:', settings);
            applyThemeSettings(settings);
            // Also update localStorage for the current user
            localStorage.setItem(getLocalStorageKey(), metaThemeSettings.getAttribute('content'));
            return;
          } catch (e) {
            console.error('Error parsing theme settings from meta tag:', e);
          }
        }
        
        // Set light theme by default
        const defaultSettings = {
          sidebarColor: 'dark',
          sidenavType: 'bg-white',
          navbarFixed: false,
          darkMode: false
        };
        
        applyThemeSettings(defaultSettings);
      }
      
      // Fetch theme settings from server
      function fetchServerThemeSettings() {
        // Only fetch from server if user is logged in
        if (!getCurrentUserId()) {
          console.log('Not fetching server settings: user not logged in');
          return Promise.resolve(false);
        }
        
        return fetch('/settings/get-theme', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => {
          if (!response.ok) {
            // If we get a permission error (403), just log it but don't treat as fatal
            if (response.status === 403) {
              console.log('Permission denied when fetching theme settings (likely not on Ultimate plan)');
              return { status: 'error', message: 'Permission denied' };
            }
            
            // For other errors, log more details and throw
            console.error('Server returned error when fetching theme settings:', response.status, response.statusText);
            throw new Error(`HTTP error: ${response.status}`);
          }
          return response.json();
        })
        .then(data => {
          console.log('Server theme settings response:', data);
          
          if (data.status === 'success' && data.theme_settings) {
            try {
              // Parse the settings
              const settings = JSON.parse(data.theme_settings);
              
              // Save to user-specific localStorage key
              localStorage.setItem(getLocalStorageKey(), data.theme_settings);
              
              // Apply the settings
              applyThemeSettings(settings);
              
              console.log('Successfully applied theme settings from server for user', data.user_id);
              return true;
            } catch (e) {
              console.error('Error parsing theme settings:', e);
              return false;
            }
          } else {
            if (data.status === 'error') {
              console.log('Error fetching theme settings:', data.message);
            } else {
              console.log('No theme settings found on server for current user');
            }
            return false;
          }
        })
        .catch(error => {
          console.error('Server fetch error:', error);
          return false;
        });
      }
      
      function applyThemeSettings(settings) {
        console.log('Applying theme settings:', settings);
        
        // Apply sidebar color
        if (settings.sidebarColor) {
          const colorBadge = document.querySelector(`.badge.filter[data-color="${settings.sidebarColor}"]`);
          if (colorBadge) {
            // Remove active class from other badges
            document.querySelectorAll('.badge.filter.active').forEach(badge => {
              badge.classList.remove('active');
            });
            
            // Add active class to the selected badge
            colorBadge.classList.add('active');
            
            // Apply the color
            const sidebar = document.querySelector('.sidenav');
            if (sidebar) {
              // Remove old gradient classes
              sidebar.className = sidebar.className.replace(/bg-gradient-\w+/g, '');
              // Add new gradient class
              sidebar.classList.add(`bg-gradient-${settings.sidebarColor}`);
            }
          }
        }
        
        // Apply sidenav type
        if (settings.sidenavType) {
          const sidenavButton = document.querySelector(`[data-class="${settings.sidenavType}"]`);
          if (sidenavButton) {
            // Remove active class from other buttons
            document.querySelectorAll('[data-class].active').forEach(button => {
              button.classList.remove('active');
            });
            
            // Add active class to the selected button
            sidenavButton.classList.add('active');
            
            // Apply the class
            const sidebar = document.querySelector('.sidenav');
            if (sidebar) {
              // Remove old classes
              sidebar.classList.remove('bg-white', 'bg-transparent', 'bg-gradient-dark');
              // Add new class
              sidebar.classList.add(settings.sidenavType);
            }
          }
        }
        
        // Apply navbar fixed
        const navbarFixedCheckbox = document.getElementById('navbarFixed');
        if (navbarFixedCheckbox && settings.navbarFixed !== undefined) {
          navbarFixedCheckbox.checked = settings.navbarFixed;
          
          // Apply the setting
          const navbar = document.querySelector('.navbar');
          if (navbar) {
            if (settings.navbarFixed) {
              navbar.classList.add('navbar-fixed');
            } else {
              navbar.classList.remove('navbar-fixed');
            }
          }
        }
        
        // Apply dark mode
        const darkModeCheckbox = document.getElementById('dark-version');
        if (darkModeCheckbox && settings.darkMode !== undefined) {
          darkModeCheckbox.checked = settings.darkMode;
          
          // Apply the setting
          const body = document.querySelector('body');
          if (body) {
            if (settings.darkMode) {
              body.classList.add('dark-version');
            } else {
              body.classList.remove('dark-version');
            }
          }
        }
        
        // Reset changed flag since we just loaded saved settings
        settingsChanged = false;
      }
      
      function markSettingsAsChanged() {
        settingsChanged = true;
        // Hide success message if it was shown
        const saveStatus = document.getElementById('save-status');
        if (saveStatus) {
          saveStatus.style.display = 'none';
        }
        
        // Change button style to indicate unsaved changes
        const saveButton = document.getElementById('save-theme-settings');
        if (saveButton) {
          saveButton.classList.remove('bg-gradient-success');
          saveButton.classList.add('bg-gradient-primary');
          saveButton.innerHTML = '<i class="material-symbols-rounded me-2">save</i> Save Theme Settings';
        }
      }
      
      function saveThemeSettings(showFeedback = false) {
        // Check if theme customization is allowed
        if (!canCustomizeTheme()) {
          if (showFeedback) {
            const saveStatus = document.getElementById('save-status');
            if (saveStatus) {
              saveStatus.textContent = 'Theme customization requires the Ultimate plan';
              saveStatus.style.display = 'block';
              saveStatus.className = 'text-sm text-warning mt-2';
              
              // Hide the message after 3 seconds
              setTimeout(() => {
                saveStatus.style.display = 'none';
              }, 3000);
            } else {
              alert('Theme customization is only available on the Ultimate plan.');
            }
          }
          return;
        }
        
        // Get current settings
        const settings = {
          sidebarColor: document.querySelector('.badge.filter.active')?.getAttribute('data-color') || 'dark',
          sidenavType: document.querySelector('[data-class].active')?.getAttribute('data-class') || 'bg-white',
          navbarFixed: document.getElementById('navbarFixed')?.checked || false,
          darkMode: document.getElementById('dark-version')?.checked || false,
          user_id: getCurrentUserId() // Store user ID in settings
        };
        
        console.log('Saving theme settings for user:', getCurrentUserId(), settings);
        
        // Save to user-specific localStorage key
        localStorage.setItem(getLocalStorageKey(), JSON.stringify(settings));
        
        // If we have CSRF token (logged in), save to server
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken && getCurrentUserId()) {
          // Show saving indicator
          const saveButton = document.getElementById('save-theme-settings');
          if (saveButton && showFeedback) {
            saveButton.innerHTML = '<i class="material-symbols-rounded me-2">hourglass_top</i> Saving...';
          }
          
          // Send to server via AJAX to save in database
          fetch('/settings/save-theme', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken.getAttribute('content')
            },
            body: JSON.stringify(settings)
          })
          .then(response => {
            if (!response.ok) {
              // Handle HTTP errors
              console.error('Server returned error:', response.status, response.statusText);
              throw new Error(`HTTP error: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            console.log('Server response:', data);
            if (data.status === 'success') {
              settingsChanged = false;
              
              if (showFeedback) {
                // Show success message
                const saveStatus = document.getElementById('save-status');
                if (saveStatus) {
                  saveStatus.textContent = 'Settings saved successfully!';
                  saveStatus.style.display = 'block';
                  saveStatus.className = 'text-sm text-success mt-2';
                }
                
                // Change button to success state
                const saveButton = document.getElementById('save-theme-settings');
                if (saveButton) {
                  saveButton.classList.remove('bg-gradient-primary');
                  saveButton.classList.add('bg-gradient-success');
                  saveButton.innerHTML = '<i class="material-symbols-rounded me-2">check</i> Saved!';
                }
                
                // Hide the success message after 3 seconds
                setTimeout(() => {
                  if (saveStatus) {
                    saveStatus.style.display = 'none';
                  }
                }, 3000);
              }
            } else {
              // Server returned success=false
              console.error('Server returned error in response:', data.message);
              throw new Error(data.message || 'Server returned an error');
            }
          })
          .catch(error => {
            console.error('Error saving theme settings:', error);
            if (showFeedback) {
              // Show error message
              const saveStatus = document.getElementById('save-status');
              if (saveStatus) {
                saveStatus.textContent = 'Failed to save settings: ' + (error.message || 'Unknown error');
                saveStatus.style.display = 'block';
                saveStatus.className = 'text-sm text-danger mt-2';
              }
              
              // Reset button state
              const saveButton = document.getElementById('save-theme-settings');
              if (saveButton) {
                saveButton.classList.remove('bg-gradient-success');
                saveButton.classList.add('bg-gradient-primary');
                saveButton.innerHTML = '<i class="material-symbols-rounded me-2">save</i> Save Theme Settings';
              }
            }
          });
        }
      }
      
      function restoreThemeSettings() {
        // Get saved settings from localStorage for the current user
        const savedSettings = localStorage.getItem(getLocalStorageKey());
        if (savedSettings) {
          try {
            const settings = JSON.parse(savedSettings);
            console.log('Restoring theme settings from localStorage for', getCurrentUserId() || 'guest', settings);
            applyThemeSettings(settings);
            return true;
          } catch (e) {
            console.error('Error parsing localStorage theme settings:', e);
            return false;
          }
        }
        return false;
      }
      
      // Warn about unsaved changes when closing the configurator
      const closeButton = document.querySelector('.fixed-plugin-close-button');
      if (closeButton) {
        closeButton.addEventListener('click', function() {
          if (settingsChanged) {
            if (confirm('You have unsaved theme settings. Save changes?')) {
              saveThemeSettings(true);
            }
          }
        });
      }
    </script>
    
    @stack('scripts')
  </body>
</html> 
@extends('layouts.admin', ['hideNavbar' => true, 'hideConfigPanel' => true])

@section('title', 'Login to Barangay') <!-- base the barangay name off the "barangay" field in the "tenants" table -->

@section('content')
@push('scripts')
<script>
    // Apply theme on login page too for consistent experience
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Get saved settings from localStorage - use guest settings on login page
            const savedSettings = localStorage.getItem('themeSettings_guest');
            if (savedSettings) {
                const settings = JSON.parse(savedSettings);
                
                // Apply dark mode if enabled
                if (settings.darkMode) {
                    document.body.classList.add('dark-version');
                }
                
                console.log('Applied guest theme settings on login page');
            }
        } catch (e) {
            console.error('Error applying theme on login page:', e);
        }
    });
    
    // Don't start loading turnstile until the page is fully loaded
    window.addEventListener('load', function() {
        // Check if Turnstile script is already loaded
        if (!document.querySelector('script[src*="turnstile/v0/api.js"]')) {
            var turnstileScript = document.createElement('script');
            turnstileScript.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
            turnstileScript.onload = function() {
                renderTurnstile();
            };
            document.body.appendChild(turnstileScript);
        } else {
            // Script already exists, just render the widget
            if (window.turnstile) {
                renderTurnstile();
            }
        }
    });

    function renderTurnstile() {
        // Clear previous widgets and render a new one
        if (window.turnstile) {
            // Remove all existing widgets first
            const container = document.getElementById('tenant-turnstile-widget');
            if (container) {
                container.innerHTML = '';
                
                window.turnstile.render('#tenant-turnstile-widget', {
                    sitekey: '{{ env('CAPTCHA_KEY') }}',
                    callback: function(token) {
                        document.getElementById('cf-turnstile-response').value = token;
                        document.getElementById('tenant-login-button').disabled = false;
                    }
                });
            }
        }
    }
</script>
@endpush

@push('styles')
<style>
    .turnstile-container-wrapper {
        min-height: 70px;
    }
</style>
@endpush

<main class="main-content mt-0">
  <div
    class="page-header align-items-start min-vh-100"
    style="
      background-image: url('https://www.lumina.com.ph/assets/news-and-blogs-photos/Real-Estate-Investment-in-Malaybalay-Heres-Why-the-Probinsya-Life-in-Bukidnon-is-Better/Real-Estate-Investment-in-Malaybalay-Heres-Why-the-Probinsya-Life-in-Bukidnon-is-Better-Lumina-Affordable-House-and-Lot-for-Sale-Philippines.webp');
    "
  >
    <span class="mask bg-gradient-dark opacity-6"></span>
    <div class="container my-auto">
      <div class="row">
        <div class="col-lg-4 col-md-8 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div
              class="card-header p-0 position-relative mt-n4 mx-3 z-index-2"
            >
              <div
                class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1"
              >
                <h4
                  class="text-white font-weight-bolder text-center mt-2 mb-0"
                >
                  Welcome to <br> Barangay {{$tenant->barangay}}
                </h4>
                <br />
              </div>
            </div>
            <div class="card-body">
              @if(session('error'))
                <div class="alert alert-danger text-white text-sm">
                  {{ session('error') }}
                </div>
              @endif
              
              <form role="form" class="text-start" method="POST" action="{{ route('tenant.login.submit') }}">
                @csrf
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus />
                </div>
                @error('email')
                <div class="text-danger text-xs">{{ $message }}</div>
                @enderror
                
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required />
                </div>
                @error('password')
                <div class="text-danger text-xs">{{ $message }}</div>
                @enderror

                <!-- Turnstile Widget -->
                <div class="turnstile-container-wrapper mb-3 d-flex justify-content-center">
                  <div id="tenant-turnstile-widget"></div>
                  <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response">
                </div>
                @error('cf-turnstile-response')
                <div class="text-danger text-xs">{{ $message }}</div>
                @enderror
                
                <div
                  class="form-check form-switch d-flex align-items-center mb-3"
                >
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="remember"
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}
                  />
                  <label class="form-check-label mb-0 ms-3" for="remember"
                    >Remember me</label
                  >
                </div>
                <div class="text-center">
                  <button
                    type="submit"
                    class="btn bg-gradient-dark w-100 my-4 mb-2"
                    id="tenant-login-button"
                    disabled
                  >
                    Sign in
                  </button>
                </div>
                
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <footer class="footer position-absolute bottom-2 py-2 w-100">
      <div class="container">
        <div class="row align-items-center justify-content-lg-between">
          <div class="col-12 col-md-6 my-auto">
            <div
              class="copyright text-center text-sm text-white text-lg-start"
            >
              ©
              <script>
                document.write(new Date().getFullYear());
              </script>
              , made with <i class="fa fa-heart" aria-hidden="true"></i> by
              <a
                href="#"
                class="font-weight-bold text-white"
                target="_blank"
                >Playpass Creative Labs</a
              >
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
  
  <!-- Support Contact Modal -->
  <div class="modal fade" id="supportModal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="supportModalLabel">Contact Support</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>If you're having trouble logging in, please contact your system administrator or support team with the following information:</p>
          <ul>
            <li><strong>Barangay:</strong> {{$tenant->barangay}}</li>
            <li><strong>Email:</strong> {{$tenant->email}}</li>
            <li><strong>Domain:</strong> {{request()->getHttpHost()}}</li>
          </ul>
          <p>The support team will help reset your password and regain access to your account.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-dark" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
  function forgotPassword() {
    var supportModal = new bootstrap.Modal(document.getElementById('supportModal'));
    supportModal.show();
  }
</script>
@endsection

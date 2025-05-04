<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <link
      rel="apple-touch-icon"
      sizes="76x76"
      href="{{ asset('assets/img/apple-icon.png') }}"
    />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" />
    <title>Sign up for Barangay Certify</title>

    <!-- Fonts and icons -->
    <link
      rel="stylesheet"
      type="text/css"
      href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900"
    />
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <script
      src="https://kit.fontawesome.com/42d5adcbca.js"
      crossorigin="anonymous"
    ></script>

    <!-- CSS Files -->
    <link
      id="pagestyle"
      href="{{ asset('assets/css/material-dashboard.css?v=3.2.0') }}"
      rel="stylesheet"
    />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Turnstile container styling -->
    <style>
      .turnstile-container-wrapper {
        min-height: 70px;
        display: flex;
        justify-content: center;
        margin-bottom: 15px;
      }
    </style>
  </head>

  <body class="bg-gray-200">
    <main class="main-content mt-0">
      <!-- Toast Notification -->
      <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="toast-header bg-danger text-white">
            <strong class="me-auto">Error</strong>
            <small>just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body" id="toastMessage">
            {{ session('error') }}
          </div>
        </div>
      </div>
      
      <div
        class="page-header align-items-start min-vh-100"
        style="
          background-image: url('https://www.lumina.com.ph/assets/news-and-blogs-photos/Real-Estate-Investment-in-Malaybalay-Heres-Why-the-Probinsya-Life-in-Bukidnon-is-Better/Real-Estate-Investment-in-Malaybalay-Heres-Why-the-Probinsya-Life-in-Bukidnon-is-Better-Lumina-Affordable-House-and-Lot-for-Sale-Philippines.webp');
        "
      >
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="container my-auto">
          <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12 col-12">
              <br />
              <br />
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
                      Sign up
                    </h4>
                    <p class="text-white text-center mb-2">
                      Modernize your Barangay in just a few steps!
                    </p>
                  </div>
                </div>
                <div class="card-body">
                  <div class="row">
                    <!-- Left side: Form inputs -->
                    <div class="col-md-6">
                      <form id="signupForm" action="{{ route('tenant.store') }}" method="POST" class="text-start">
                        @csrf
                        <div class="input-group input-group-outline my-3">
                          <label class="form-label">Full Name</label>
                          <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="input-group input-group-outline my-3">
                          <label class="form-label">Email</label>
                          <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="input-group input-group-outline mb-3">
                          <label class="form-label">Barangay</label>
                          <input type="text" name="barangay" class="form-control" required>
                        </div>
                        <input type="hidden" name="subscription_plan" id="subscription_plan" value="Basic">
                        
                        <!-- Turnstile Widget -->
                        <div class="turnstile-container-wrapper">
                          <div id="signup-turnstile-widget"></div>
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
                            id="terms"
                            required
                          />
                          <label class="form-check-label mb-0 ms-3" for="terms">
                            I agree to the <a href="#">Terms and Conditions</a>
                          </label>
                        </div>
                        <div class="text-center">
                          <button
                            type="submit"
                            class="btn bg-gradient-dark w-100 my-4 mb-2"
                            id="signup-button"
                            disabled
                          >
                            Sign up
                          </button>
                        </div>
                        <p class="mt-4 text-sm text-center">
                          Already have an account?<br />
                          Please check your email for login instructions.
                        </p>
                      </form>
                    </div>

                    <!-- Right side: Plan picker -->
                    <div class="col-md-6">
                      <br />
                      <h5 class="text-center mb-4">Choose a Plan</h5>

                      <!-- Basic Plan -->
                      <div class="card mb-3">
                        <div class="card-body">
                          <div class="form-check mb-2">
                            <input
                              class="form-check-input plan-radio"
                              type="radio"
                              name="plan"
                              id="basic"
                              value="Basic"
                              checked
                            />
                            <label class="form-check-label fw-bold" for="basic">
                              Basic – ₱399
                            </label>
                          </div>
                          <ul class="mb-0 ps-4">
                            <li>Custom Page Headers</li>
                            <li>
                              Create up to 3 users (admin account included)
                            </li>
                            <li>30 day free trial (no commitment)</li>
                            <li>1 Month Subscription</li>
                          </ul>
                        </div>
                      </div>

                      <!-- Essentials Plan -->
                      <div class="card mb-3">
                        <div class="card-body">
                          <div class="form-check mb-2">
                            <input
                              class="form-check-input plan-radio"
                              type="radio"
                              name="plan"
                              id="essentials"
                              value="Essentials"
                            />
                            <label
                              class="form-check-label fw-bold"
                              for="essentials"
                            >
                              Essentials – ₱799
                            </label>
                          </div>
                          <ul class="mb-0 ps-4">
                            <li>Everything in Basic Plan</li>
                            <li>Custom Page Size</li>
                            <li>
                              Create up to 5 users (excluding admin account)
                            </li>
                            <li>6 Month Subscription</li>
                          </ul>
                        </div>
                      </div>

                      <!-- Ultimate Plan -->
                      <div class="card mb-3">
                        <div class="card-body">
                          <div class="form-check mb-2">
                            <input
                              class="form-check-input plan-radio"
                              type="radio"
                              name="plan"
                              id="ultimate"
                              value="Ultimate"
                            />
                            <label
                              class="form-check-label fw-bold"
                              for="ultimate"
                            >
                              Ultimate – ₱1299
                            </label>
                          </div>
                          <ul class="mb-0 ps-4">
                            <li>Everything in Essentials</li>
                            <li>Customizable Dashboard Theme</li>
                            <li>Create unlimited user accounts</li>
                            <li>Software Updates</li>
                            <li>12 Month Subscription</li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <br /><br /><br />
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
                    href="https://www.creative-tim.com"
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
    </main>

    <!-- Core JS Files -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    
    <!-- Turnstile Script -->
    <script>
      // Load Turnstile after the page is loaded
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
          const container = document.getElementById('signup-turnstile-widget');
          if (container) {
            container.innerHTML = '';
            
            window.turnstile.render('#signup-turnstile-widget', {
              sitekey: '{{ env('CAPTCHA_KEY') }}',
              callback: function(token) {
                document.getElementById('cf-turnstile-response').value = token;
                document.getElementById('signup-button').disabled = false;
              }
            });
          }
        }
      }
    </script>
    
    <script>
      var win = navigator.platform.indexOf("Win") > -1;
      if (win && document.querySelector("#sidenav-scrollbar")) {
        var options = { damping: "0.5" };
        Scrollbar.init(document.querySelector("#sidenav-scrollbar"), options);
      }
      
      // Handle plan selection
      document.querySelectorAll('.plan-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
          document.getElementById('subscription_plan').value = this.value;
        });
      });
      
      // Form submission handled in the DOM content loaded event below
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>
    
    <!-- Toast notification initialization -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, checking for error messages');
        
        // Check if there's an error in the session
        @if(session('error'))
          console.log('Error found in session: {{ session('error') }}');
          // Get the toast element
          const toastElement = document.getElementById('errorToast');
          console.log('Toast element found:', toastElement);
          
          // Initialize the toast
          try {
            const toast = new bootstrap.Toast(toastElement, {
              autohide: true,
              delay: 5000
            });
            console.log('Toast initialized successfully');
            
            // Show the toast
            toast.show();
            console.log('Toast shown');
          } catch (error) {
            console.error('Error initializing toast:', error);
          }
        @else
          console.log('No error in session');
        @endif
        
        // Form validation
        const form = document.getElementById('signupForm');
        form.addEventListener('submit', function(event) {
          const termsCheckbox = document.getElementById('terms');
          const turnstileResponse = document.getElementById('cf-turnstile-response').value;
          
          if (!termsCheckbox.checked) {
            event.preventDefault();
            console.log('Form submission prevented: Terms not accepted');
            
            // Show toast for terms error
            document.getElementById('toastMessage').textContent = 'Please agree to the Terms and Conditions';
            try {
              const toast = new bootstrap.Toast(document.getElementById('errorToast'));
              toast.show();
              console.log('Terms error toast shown');
            } catch (error) {
              console.error('Error showing terms toast:', error);
              alert('Please agree to the Terms and Conditions');
            }
          } else if (!turnstileResponse) {
            event.preventDefault();
            console.log('Form submission prevented: Captcha not completed');
            
            // Show toast for captcha error
            document.getElementById('toastMessage').textContent = 'Please complete the captcha verification';
            try {
              const toast = new bootstrap.Toast(document.getElementById('errorToast'));
              toast.show();
              console.log('Captcha error toast shown');
            } catch (error) {
              console.error('Error showing captcha toast:', error);
              alert('Please complete the captcha verification');
            }
          } else {
            console.log('Form submission accepted: All validations passed');
          }
        });
      });
    </script>
  </body>
</html>

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
    <title>Set Up Your Password - BarangayCertify</title>
    <!--     Fonts and icons     -->
    <link
      rel="stylesheet"
      type="text/css"
      href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900"
    />
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
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
      href="{{ asset('assets/css/material-dashboard.css?v=3.2.0') }}"
      rel="stylesheet"
    />
  </head>

  <body class="bg-gray-200">
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
            <div class="col-lg-5 col-md-8 col-12 mx-auto">
              <div class="card z-index-0 fadeIn3 fadeInBottom">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                  <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                    <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Set Up Your Password</h4>
                  </div>
                </div>
                <div class="card-body">
                  @if(session('error'))
                    <div class="alert alert-danger text-white">
                      {{ session('error') }}
                    </div>
                  @endif
                  
                  @if(session('success') || isset($success))
                    <div class="alert alert-success text-white">
                      {{ session('success') ?? $success ?? 'Password set successfully!' }}
                      <p>You can now <a href="{{ session('domain_url') ?? $domain_url ?? '#' }}" class="text-white font-weight-bold">log in to your domain</a>.</p>
                    </div>
                  @elseif(isset($error))
                    <div class="alert alert-danger text-white">
                      {{ $error }}
                    </div>
                  @else
                    <div class="text-center">
                      <p class="mb-4">Welcome to BarangayCertify! Please create a secure password to complete your account setup.</p>
                    </div>
                    
                    <form method="POST" action="{{ url('/complete-setup') }}" id="passwordForm">
                      @csrf
                      <input type="hidden" name="tenant_id" value="{{ $tenant_id }}">
                      <input type="hidden" name="token" value="{{ $token }}">
                      
                      <div class="input-group input-group-outline my-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                      </div>
                      @error('password')
                        <div class="text-danger text-xs">{{ $message }}</div>
                      @enderror
                      
                      <div class="input-group input-group-outline mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                      </div>
                      
                      <div class="password-requirements mb-3">
                        <p class="text-xs">Password must contain:</p>
                        <ul class="text-xs ps-4 mb-0">
                          <li id="length-check">At least 8 characters</li>
                          <li id="uppercase-check">At least 1 uppercase letter</li>
                          <li id="number-check">At least 1 number</li>
                          <li id="special-check">At least 1 special character</li>
                        </ul>
                      </div>
                      
                      <div class="text-center">
                        <button type="submit" id="submit-btn" class="btn bg-gradient-primary w-100 my-4 mb-2" disabled>
                          Create Password & Complete Setup
                        </button>
                      </div>
                    </form>
                  @endif
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
                    href="https://www.creative-tim.com"
                    class="font-weight-bold text-white"
                    target="_blank"
                    >BarangayCertify</a
                  >
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </main>
    <!--   Core JS Files   -->
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    
    <!-- Password validation script -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const submitBtn = document.getElementById('submit-btn');
        const lengthCheck = document.getElementById('length-check');
        const uppercaseCheck = document.getElementById('uppercase-check');
        const numberCheck = document.getElementById('number-check');
        const specialCheck = document.getElementById('special-check');
        
        function validatePassword() {
          const password = passwordInput.value;
          const confirmPassword = confirmInput.value;
          
          // Check password requirements
          const hasLength = password.length >= 8;
          const hasUppercase = /[A-Z]/.test(password);
          const hasNumber = /[0-9]/.test(password);
          const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
          const passwordsMatch = password === confirmPassword && password !== '';
          
          // Update requirement indicators
          lengthCheck.className = hasLength ? 'text-success' : '';
          uppercaseCheck.className = hasUppercase ? 'text-success' : '';
          numberCheck.className = hasNumber ? 'text-success' : '';
          specialCheck.className = hasSpecial ? 'text-success' : '';
          
          // Enable submit button if all requirements are met
          submitBtn.disabled = !(hasLength && hasUppercase && hasNumber && hasSpecial && passwordsMatch);
        }
        
        passwordInput.addEventListener('input', validatePassword);
        confirmInput.addEventListener('input', validatePassword);
      });
    </script>
    
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>
  </body>
</html> 
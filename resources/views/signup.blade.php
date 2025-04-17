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
                  @if(session('error'))
                    <div class="alert alert-danger text-white">
                      {{ session('error') }}
                    </div>
                  @endif
                  
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
                            <li>Page Watermarking</li>
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
      
      // Form submission
      document.getElementById('signupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!document.getElementById('terms').checked) {
          alert('Please agree to the Terms and Conditions');
          return;
        }
        
        // Submit the form
        this.submit();
      });
    </script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="{{ asset('assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>
  </body>
</html>

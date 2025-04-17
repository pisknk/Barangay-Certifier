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
    <title>Thank you!</title>
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
            <div class="col-lg-4 col-md-8 col-12 mx-auto">
              <div class="card z-index-0 fadeIn3 fadeInBottom">
                <div class="card-body">
                  <div class="text-center">
                    <img
                      src="https://em-content.zobj.net/source/telegram/386/envelope_2709-fe0f.webp"
                      alt="Mail"
                      style="width: 6rem; height: 6rem"
                    />
                    <br />
                    <br />
                    <h4>Thank you for signing up!</h4>
                    <p>
                      We will send you an email <br />
                      if your website is ready to go!
                    </p>
                    <a
                      href="/"
                      class="btn bg-gradient-dark w-100 my-4 mb-2"
                    >
                      Return to Landing Page
                    </a>
                  </div>
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
                    >Playpass Creative Labs</a
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
    <script>
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
    <script src="{{ asset('assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>
  </body>
</html>

<!DOCTYPE html>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">
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

    <title>Barangay Certify</title>

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
      href="{{ asset('assets/css/material-dashboard.css?v=3.1.0') }}"
      rel="stylesheet"
    />
  </head>

  <body class="index-page bg-gray-200">
    <!-- Navbar -->
    <div class="container position-sticky z-index-sticky top-0">
      <div class="row">
        <div class="col-12">
          <nav
            class="navbar navbar-expand-lg blur border-radius-xl top-0 z-index-fixed shadow position-absolute my-3 p-2 start-0 end-0 mx-4"
          >
            <div class="container-fluid px-0">
              <a
                class="navbar-brand font-weight-bolder ms-sm-3 text-sm"
                href="https://demos.creative-tim.com/material-kit/index"
                rel="tooltip"
                title="Designed and Coded by Creative Tim"
                data-placement="bottom"
                target="_blank"
              >
                Barangay Certify
              </a>
              <div
                class="collapse navbar-collapse pt-3 pb-2 py-lg-0 w-100"
                id="navigation"
              >
                <ul class="navbar-nav navbar-nav-hover ms-auto">
                  <li class="nav-item my-auto ms-3 ms-lg-0">
                    <a
                      href="{{ route('signup') }}"
                      class="btn bg-gradient-dark mb-0 mt-2 mt-md-0"
                      >Signup for our service!</a
                    >
                  </li>
                </ul>
              </div>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>
      </div>
    </div>

    <header class="header-2">
      <div
        class="page-header min-vh-75 relative"
        style="
          background-image: url('https://malaybalaycity.gov.ph/wp-content/uploads/2022/08/CITYHALL-scaled.jpg');
        "
      >
        <span class="mask bg-gradient-dark opacity-8"></span>
        <div class="container">
          <div class="row">
            <div class="col-lg-7 text-center mx-auto">
              <br />
              <br />
              <h1 class="text-white font-weight-black pt-3 mt-n5">
                Barangay Certify
              </h1>
              <p class="lead text-white mt-3">
                From cedulas to community documents, Barangay Certify <br />
                makes local governance digital, efficient, and hassle-free.
              </p>
              <div class="mt-4">
                <a href="#features" class="btn bg-gradient-primary me-2"
                  >Learn More</a
                >
                <a href="#pricing" class="btn bg-gradient-light">View Plans</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="card card-body blur shadow-blur mx-3 mx-md-4 mt-n6">
      <section class="pt-3 pb-4" id="count-stats">
        <div class="container">
          <div class="row">
            <div class="col-lg-9 mx-auto py-3">
              <div class="row">
                <div class="col-md-4 position-relative">
                  <div class="p-3 text-center">
                    <h1 class="text-gradient text-dark">
                      <span id="state1" countTo="100">0</span>+
                    </h1>
                    <h5 class="mt-3">Barangays Served</h5>
                    <p class="text-sm font-weight-normal">
                      Trusted by barangays across the Philippines to streamline
                      their document processing.
                    </p>
                  </div>
                  <hr class="vertical dark" />
                </div>
                <div class="col-md-4 position-relative">
                  <div class="p-3 text-center">
                    <h1 class="text-gradient text-dark">
                      <span id="state2" countTo="15">0</span>+
                    </h1>
                    <h5 class="mt-3">Document Types</h5>
                    <p class="text-sm font-weight-normal">
                      From barangay clearance to business permits, we support
                      all essential documents.
                    </p>
                  </div>
                  <hr class="vertical dark" />
                </div>
                <div class="col-md-4">
                  <div class="p-3 text-center">
                    <h1
                      class="text-gradient text-dark"
                      id="state3"
                      countTo="99"
                    >
                      0
                    </h1>
                    <h5 class="mt-3">% Uptime</h5>
                    <p class="text-sm font-weight-normal">
                      Reliable cloud-based service ensuring your barangay
                      operations never stop.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="my-5 py-5" id="features">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-6">
              <h2 class="text-gradient text-primary mb-4">
                Powerful Features for Modern Barangays
              </h2>
              <div class="row">
                <div class="col-md-6">
                  <div class="info">
                    <i
                      class="material-symbols-rounded text-gradient text-success text-3xl"
                      >description</i
                    >
                    <h5 class="font-weight-bolder mt-3">
                      Auto-Generated Certificates
                    </h5>
                    <p>
                      Generate barangay clearance, residency certificates, and
                      other documents in seconds with our smart templates.
                    </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info">
                    <i
                      class="material-symbols-rounded text-gradient text-success text-3xl"
                      >receipt_long</i
                    >
                    <h5 class="font-weight-bolder mt-3">Cedula Processing</h5>
                    <p>
                      Complete cedula management system with automated
                      calculations and official formatting.
                    </p>
                  </div>
                </div>
              </div>
              <div class="row mt-4">
                <div class="col-md-6">
                  <div class="info">
                    <i
                      class="material-symbols-rounded text-gradient text-success text-3xl"
                      >storage</i
                    >
                    <h5 class="font-weight-bolder mt-3">
                      Multi-Tenant Architecture
                    </h5>
                    <p>
                      Each barangay gets its own secure, isolated database
                      ensuring complete data privacy.
                    </p>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info">
                    <i
                      class="material-symbols-rounded text-gradient text-success text-3xl"
                      >groups</i
                    >
                    <h5 class="font-weight-bolder mt-3">Multi-User Access</h5>
                    <p>
                      Create multiple user accounts with different permission
                      levels for your barangay staff.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-5 ms-auto me-auto p-lg-4 mt-lg-0 mt-4">
              <img
                src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                class="img-fluid border-radius-lg shadow"
                alt="Barangay Certify Dashboard"
              />
            </div>
          </div>
        </div>
      </section>

      <section class="py-5">
        <div class="container">
          <div class="row">
            <div class="col-lg-6 mx-auto text-center">
              <h3 class="mb-5">What Barangay Officials Say</h3>
            </div>
          </div>
          <div class="row mt-4">
            <div class="col-md-4">
              <div class="card p-4">
                <div class="d-flex">
                  <div class="me-3">
                    <img
                      src="https://randomuser.me/api/portraits/women/32.jpg"
                      class="avatar avatar-sm rounded-circle"
                      alt="User"
                    />
                  </div>
                  <div>
                    <h6 class="mb-0">Barangay Captain Maria Santos</h6>
                    <p class="text-sm text-muted">Barangay 123, Quezon City</p>
                  </div>
                </div>
                <p class="mt-3">
                  "Barangay Certify reduced our document processing time from
                  hours to minutes. Our constituents are much happier with the
                  faster service."
                </p>
              </div>
            </div>
            <div class="col-md-4 mt-4 mt-md-0">
              <div class="card p-4">
                <div class="d-flex">
                  <div class="me-3">
                    <img
                      src="https://randomuser.me/api/portraits/men/45.jpg"
                      class="avatar avatar-sm rounded-circle"
                      alt="User"
                    />
                  </div>
                  <div>
                    <h6 class="mb-0">Barangay Secretary Juan Dela Cruz</h6>
                    <p class="text-sm text-muted">Barangay 456, Manila</p>
                  </div>
                </div>
                <p class="mt-3">
                  "The multi-tenant system gives us peace of mind knowing our
                  data is secure and separate from other barangays. The
                  interface is very user-friendly too."
                </p>
              </div>
            </div>
            <div class="col-md-4 mt-4 mt-md-0">
              <div class="card p-4">
                <div class="d-flex">
                  <div class="me-3">
                    <img
                      src="https://randomuser.me/api/portraits/women/68.jpg"
                      class="avatar avatar-sm rounded-circle"
                      alt="User"
                    />
                  </div>
                  <div>
                    <h6 class="mb-0">Treasurer Ana Reyes</h6>
                    <p class="text-sm text-muted">Barangay 789, Cebu City</p>
                  </div>
                </div>
                <p class="mt-3">
                  "Cedula processing used to be our biggest headache. Now with
                  automatic calculations and printing, we've eliminated errors
                  and reduced complaints."
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="py-5 bg-gray-100" id="pricing">
        <div class="container">
          <div class="row text-center">
            <div class="col-lg-8 mx-auto">
              <h2 class="text-gradient text-primary mb-4">
                Simple, Transparent Pricing
              </h2>
              <p class="lead">
                Choose the perfect plan for your barangay's needs
              </p>
            </div>
          </div>
          <div class="row mt-5">
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-header bg-white pt-4 pb-3">
                  <h5 class="font-weight-bolder">Basic</h5>
                  <h2 class="font-weight-bolder mt-3">
                    ₱399<span class="text-sm">/month</span>
                  </h2>
                </div>
                <div class="card-body">
                  <ul class="list-group">
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> Custom Page
                      Headers
                    </li>
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> Create up to 3
                      users
                    </li>
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> 30 day free trial
                    </li>
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> 1 Month
                      Subscription
                    </li>
                  </ul>
                </div>
                <div class="card-footer bg-white">
                  <a href="{{ route('signup') }}" class="btn bg-gradient-primary w-100"
                    >Get Started</a
                  >
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-header bg-gradient-primary pt-4 pb-3">
                  <h5 class="font-weight-bolder text-white">Essentials</h5>
                  <h2 class="font-weight-bolder mt-3 text-white">
                    ₱799<span class="text-sm text-white opacity-8">/month</span>
                  </h2>
                </div>
                <div class="card-body">
                  <ul class="list-group">
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> Everything in
                      Basic
                    </li>
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> Custom Page Size
                    </li>
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> Create up to 5
                      users
                    </li>
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> 6 Month
                      Subscription
                    </li>
                  </ul>
                </div>
                <div class="card-footer bg-white">
                  <a href="{{ route('signup') }}" class="btn bg-gradient-primary w-100"
                    >Get Started</a
                  >
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-header bg-white pt-4 pb-3">
                  <h5 class="font-weight-bolder">Ultimate</h5>
                  <h2 class="font-weight-bolder mt-3">
                    ₱1,299<span class="text-sm">/month</span>
                  </h2>
                </div>
                <div class="card-body">
                  <ul class="list-group">
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> Everything in
                      Essentials
                    </li>
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> Custom Dashboard
                      Theme
                    </li>
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> Unlimited user
                      accounts
                    </li>
                    <li class="list-group-item border-0 ps-0">
                      <span class="text-success me-2">✓</span> 12 Month
                      Subscription
                    </li>
                  </ul>
                </div>
                <div class="card-footer bg-white">
                  <a href="{{ route('signup') }}" class="btn bg-gradient-primary w-100"
                    >Get Started</a
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="py-5">
        <div class="container">
          <div class="row text-center">
            <div class="col-lg-8 mx-auto">
              <h2 class="text-gradient text-primary mb-4">
                Supported Documents
              </h2>
              <p class="lead">
                All essential barangay documents in one platform
              </p>
            </div>
          </div>
          <div class="row mt-5">
            <div class="col-md-3 col-sm-6 mb-4">
              <div class="card h-100 p-3 text-center">
                <div
                  class="icon icon-shape bg-gradient-primary shadow text-center border-radius-lg mb-3"
                >
                  <i class="material-symbols-rounded text-white">description</i>
                </div>
                <h5 class="font-weight-bolder">Barangay Clearance</h5>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
              <div class="card h-100 p-3 text-center">
                <div
                  class="icon icon-shape bg-gradient-primary shadow text-center border-radius-lg mb-3"
                >
                  <i class="material-symbols-rounded text-white">home</i>
                </div>
                <h5 class="font-weight-bolder">Residency Certificate</h5>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
              <div class="card h-100 p-3 text-center">
                <div
                  class="icon icon-shape bg-gradient-primary shadow text-center border-radius-lg mb-3"
                >
                  <i class="material-symbols-rounded text-white"
                    >receipt_long</i
                  >
                </div>
                <h5 class="font-weight-bolder">Cedula</h5>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-4">
              <div class="card h-100 p-3 text-center">
                <div
                  class="icon icon-shape bg-gradient-primary shadow text-center border-radius-lg mb-3"
                >
                  <i class="material-symbols-rounded text-white">business</i>
                </div>
                <h5 class="font-weight-bolder">Business Permit</h5>
              </div>
            </div>
          </div>
          <div class="text-center mt-4">
            <a href="#" class="btn btn-link text-primary"
              >View all document types →</a
            >
          </div>
        </div>
      </section>

      <!-- -------   START PRE-FOOTER 2 - simple social line w/ title & 3 buttons    -------- -->
      <div class="py-5">
        <div class="container">
          <div class="row">
            <div class="col-lg-5 ms-auto">
              <h4 class="mb-1">Thank you for your support!</h4>
              <p class="lead mb-0">We deliver the best web products</p>
            </div>
            <div class="col-lg-5 me-lg-auto my-lg-auto text-lg-end mt-5">
              <a
                href="https://twitter.com/intent/tweet?text=Check%20Barangay%20Certify%20for%20your%20barangay%20document%20needs"
                class="btn btn-twitter mb-0 me-2"
                target="_blank"
              >
                <i class="fab fa-twitter me-1"></i> Tweet
              </a>
              <a
                href="https://www.facebook.com/sharer/sharer.php?u=https://yourwebsite.com"
                class="btn btn-facebook mb-0 me-2"
                target="_blank"
              >
                <i class="fab fa-facebook-square me-1"></i> Share
              </a>
              <a
                href="https://www.pinterest.com/pin/create/button/?url=https://yourwebsite.com"
                class="btn btn-pinterest mb-0 me-2"
                target="_blank"
              >
                <i class="fab fa-pinterest me-1"></i> Pin it
              </a>
            </div>
          </div>
        </div>
      </div>
      <!-- -------   END PRE-FOOTER 2 - simple social line w/ title & 3 buttons    -------- -->
    </div>

    <footer class="footer pt-5 mt-5">
      <div class="container">
        <div class="row">
          <div class="col-md-3 mb-4 ms-auto">
            <div>
              <h5 class="font-weight-bolder mb-4">Barangay Certify</h5>
              <p>Digital transformation for Philippine barangays.</p>
            </div>
            <div>
              <ul class="d-flex flex-row ms-n3 nav">
                <li class="nav-item">
                  <a class="nav-link pe-1" href="#" target="_blank">
                    <i class="fab fa-facebook text-lg opacity-8"></i>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link pe-1" href="#" target="_blank">
                    <i class="fab fa-twitter text-lg opacity-8"></i>
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-md-2 col-sm-6 col-6 mb-4">
            <div>
              <h6 class="text-sm">Company</h6>
              <ul class="flex-column ms-n3 nav">
                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank"> About Us </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank"> Features </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="#pricing"> Pricing </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank"> Blog </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-md-2 col-sm-6 col-6 mb-4">
            <div>
              <h6 class="text-sm">Resources</h6>
              <ul class="flex-column ms-n3 nav">
                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank">
                    Documentation
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank"> Tutorials </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank"> Support </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-md-2 col-sm-6 col-6 mb-4">
            <div>
              <h6 class="text-sm">Help & Support</h6>
              <ul class="flex-column ms-n3 nav">
                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank"> Contact Us </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank">
                    Knowledge Center
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank">
                    Custom Development
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-md-2 col-sm-6 col-6 mb-4 me-auto">
            <div>
              <h6 class="text-sm">Legal</h6>
              <ul class="flex-column ms-n3 nav">
                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank">
                    Terms & Conditions
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank">
                    Privacy Policy
                  </a>
                </li>

                <li class="nav-item">
                  <a class="nav-link" href="#" target="_blank"> Licenses </a>
                </li>
              </ul>
            </div>
          </div>

          <div class="col-12">
            <div class="text-center">
              <p class="text-dark my-4 text-sm font-weight-normal">
                All rights reserved. Copyright ©
                <script>
                  document.write(new Date().getFullYear());
                </script>
                Barangay Certify.
              </p>
            </div>
          </div>
        </div>
      </div>
    </footer>

    <!--   Core JS Files   -->
    <script
      src="{{ asset('assets/js/core/popper.min.js') }}"
      type="text/javascript"
    ></script>
    <script
      src="{{ asset('assets/js/core/bootstrap.min.js') }}"
      type="text/javascript"
    ></script>
    <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/countup.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>

    <!-- Control Center for Material UI Kit -->
    <script
      src="{{ asset('assets/js/material-dashboard.min.js?v=3.1.0') }}"
      type="text/javascript"
    ></script>

    <script type="text/javascript">
      if (document.getElementById("state1")) {
        const countUp = new CountUp(
          "state1",
          document.getElementById("state1").getAttribute("countTo")
        );
        if (!countUp.error) {
          countUp.start();
        } else {
          console.error(countUp.error);
        }
      }
      if (document.getElementById("state2")) {
        const countUp1 = new CountUp(
          "state2",
          document.getElementById("state2").getAttribute("countTo")
        );
        if (!countUp1.error) {
          countUp1.start();
        } else {
          console.error(countUp1.error);
        }
      }
      if (document.getElementById("state3")) {
        const countUp2 = new CountUp(
          "state3",
          document.getElementById("state3").getAttribute("countTo")
        );
        if (!countUp2.error) {
          countUp2.start();
        } else {
          console.error(countUp2.error);
        }
      }
    </script>
  </body>
</html>

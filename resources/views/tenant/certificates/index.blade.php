@extends('layouts.tenant')

@section('title', 'Select Certificate Type')

@section('content')
<br><br><br>
<div class="container-fluid py-2">
  <!-- Display flash messages -->
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif

  <div class="row">
    <div class="ms-3 d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="mb-0 h4 font-weight-bolder">Select a Type of Certificate</h3>
        <p class="mb-0">
          Choose the certificate you want to issue
        </p>
      </div>
    </div>
    
    <div class="row mt-3">
      <!-- Barangay Clearance -->
      <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <a href="{{ route('tenant.certificates.form', 'barangay_clearance') }}" class="text-decoration-none">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center mb-3">
                <div class="icon icon-shape icon-md bg-gradient-dark shadow text-center">
                  <i class="material-symbols-rounded opacity-10">verified</i>
                </div>
                <div class="ms-3">
                  <h5 class="mb-0 font-weight-bold">Barangay Clearance</h5>
                </div>
              </div>
              <p class="mb-0 text-sm">
                Official document certifying good moral character and absence of pending case records.
              </p>
            </div>
          </div>
        </a>
      </div>
      
      <!-- Certificate of Indigency -->
      <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <a href="{{ route('tenant.certificates.form', 'indigency') }}" class="text-decoration-none">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center mb-3">
                <div class="icon icon-shape icon-md bg-gradient-dark shadow text-center">
                  <i class="material-symbols-rounded opacity-10">folder_special</i>
                </div>
                <div class="ms-3">
                  <h5 class="mb-0 font-weight-bold">Certificate of Indigency</h5>
                </div>
              </div>
              <p class="mb-0 text-sm">
                Document certifying a resident's low-income status for government assistance programs.
              </p>
            </div>
          </div>
        </a>
      </div>
      
      <!-- Certificate of Residency -->
      <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <a href="{{ route('tenant.certificates.form', 'residency') }}" class="text-decoration-none">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center mb-3">
                <div class="icon icon-shape icon-md bg-gradient-dark shadow text-center">
                  <i class="material-symbols-rounded opacity-10">home</i>
                </div>
                <div class="ms-3">
                  <h5 class="mb-0 font-weight-bold">Certificate of Residency</h5>
                </div>
              </div>
              <p class="mb-0 text-sm">
                Confirms that a person is a legitimate resident of the barangay.
              </p>
            </div>
          </div>
        </a>
      </div>
      
      <!-- Business Clearance -->
      <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <a href="{{ route('tenant.certificates.form', 'business_clearance') }}" class="text-decoration-none">
          <div class="card h-100">
            <div class="card-body p-3">
              <div class="d-flex align-items-center mb-3">
                <div class="icon icon-shape icon-md bg-gradient-dark shadow text-center">
                  <i class="material-symbols-rounded opacity-10">store</i>
                </div>
                <div class="ms-3">
                  <h5 class="mb-0 font-weight-bold">Business Clearance</h5>
                </div>
              </div>
              <p class="mb-0 text-sm">
                Required document for business permit applications operating within the barangay.
              </p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
  
  <footer class="footer py-4">
    <div class="container-fluid">
      <div class="row align-items-center justify-content-lg-between">
        <div class="col-lg-6 mb-lg-0 mb-4">
          <div class="copyright text-center text-sm text-muted text-lg-start">
            ©
            <script>
              document.write(new Date().getFullYear());
            </script>
            , made with <i class="fa fa-heart"></i> by
            <a href="#" class="font-weight-bold" target="_blank">Playpass Creative Labs</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</div>
@endsection 
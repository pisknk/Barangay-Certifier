@extends('layouts.tenant')

@section('title')
  @if($type == 'barangay_clearance')
    Barangay Clearance Application
  @elseif($type == 'indigency')
    Certificate of Indigency Application
  @elseif($type == 'residency')
    Certificate of Residency Application
  @elseif($type == 'business_clearance')
    Business Clearance Application
  @endif
@endsection

@section('content')
<style>
  .form-floating > .form-control {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    height: calc(3.5rem + 2px);
    line-height: 1.25;
  }
  .form-floating > .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  }
  .form-floating > .form-control::placeholder {
    color: transparent;
  }
  .form-floating > .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
  }
  .form-floating > .form-control:-webkit-autofill {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
  }
  .form-floating > .form-control:focus ~ label,
  .form-floating > .form-control:not(:placeholder-shown) ~ label {
    opacity: 0.65;
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
  }
  .form-check-input {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
  }
</style>

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
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 px-3">
            @if($type == 'barangay_clearance')
              <h6 class="text-white text-capitalize ps-3">Barangay Clearance Application</h6>
            @elseif($type == 'indigency')
              <h6 class="text-white text-capitalize ps-3">Certificate of Indigency Application</h6>
            @elseif($type == 'residency')
              <h6 class="text-white text-capitalize ps-3">Certificate of Residency Application</h6>
            @elseif($type == 'business_clearance')
              <h6 class="text-white text-capitalize ps-3">Business Clearance Application</h6>
            @endif
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="px-4">
            <form method="POST" action="{{ route('tenant.certificates.submit', $type) }}">
              @csrf
              <input type="hidden" name="certificate_type" value="{{ $type }}">

              <!-- Common fields for all certificates -->
              <div class="row mb-3">
                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full Name" required>
                    <label for="full_name">Full Name</label>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="address" name="address" placeholder="Purok" required>
                    <label for="address">Purok</label>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4 mb-3">
                  <div class="form-floating">
                    <input type="number" class="form-control" id="age" name="age" placeholder="Age" required>
                    <label for="age">Age</label>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="civil_status" name="civil_status" placeholder="Civil Status" required>
                    <label for="civil_status">Civil Status</label>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="contact_number" name="contact_number" placeholder="Contact Number" required>
                    <label for="contact_number">Contact Number</label>
                  </div>
                </div>
              </div>

              <!-- Certificate-specific fields -->
              @if($type == 'business_clearance')
              <div class="row mb-3">
                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="business_name" name="business_name" placeholder="Business Name" required>
                    <label for="business_name">Business Name</label>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="business_address" name="business_address" placeholder="Business Address" required>
                    <label for="business_address">Business Address</label>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-12 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="business_nature" name="business_nature" placeholder="Business Nature" required>
                    <label for="business_nature">Business Nature</label>
                  </div>
                </div>
              </div>
              @endif

              @if($type == 'indigency')
              <div class="row mb-3">
                <div class="col-md-12 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="purpose" name="purpose" placeholder="Purpose" required>
                    <label for="purpose">Purpose</label>
                  </div>
                </div>
              </div>
              @endif

              @if($type == 'barangay_clearance')
              <div class="row mb-3">
                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="date" class="form-control" id="birthdate" name="birthdate" placeholder="Birthdate">
                    <label for="birthdate">Birthdate</label>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="since_year" name="since_year" placeholder="e.g. 1975">
                    <label for="since_year">Resident Since (Year)</label>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-12 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="purpose" name="purpose" placeholder="e.g. FOR APPLICATION OF PROBATION">
                    <label for="purpose">Purpose</label>
                  </div>
                </div>
              </div>
              @endif
              
              @if($type == 'residency')
              <div class="row mb-3">
                <div class="col-md-12 mb-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="purpose" name="purpose" placeholder="e.g. FOR IDENTIFICATION">
                    <label for="purpose">Purpose</label>
                  </div>
                </div>
              </div>
              @endif

              <div class="row mb-3">
                <div class="col-md-12">
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                      I hereby certify that the information provided is true and correct.
                    </label>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between">
                  <a href="{{ route('tenant.certificates.index') }}" class="btn btn-outline-secondary">Back</a>
                  <button type="submit" class="btn bg-gradient-dark">Submit Application</button>
                </div>
              </div>
            </form>
          </div>
        </div>
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
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
                <div class="col-md-6">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" name="full_name" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Purok</label>
                    <input type="text" class="form-control" name="address" required>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-md-4">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Age</label>
                    <input type="number" class="form-control" name="age" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Civil Status</label>
                    <input type="text" class="form-control" name="civil_status" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" class="form-control" name="contact_number" required>
                  </div>
                </div>
              </div>

              <!-- Certificate-specific fields -->
              @if($type == 'business_clearance')
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Business Name</label>
                    <input type="text" class="form-control" name="business_name" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Business Address</label>
                    <input type="text" class="form-control" name="business_address" required>
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-12">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Business Nature</label>
                    <input type="text" class="form-control" name="business_nature" required>
                  </div>
                </div>
              </div>
              @endif

              @if($type == 'indigency')
              <div class="row mb-3">
                <div class="col-md-12">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Purpose</label>
                    <input type="text" class="form-control" name="purpose" required>
                  </div>
                </div>
              </div>
              @endif

              @if($type == 'barangay_clearance')
              <div class="row mb-3">
                <div class="col-md-6">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Birthdate</label>
                    <input type="date" class="form-control" name="birthdate">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Resident Since (Year)</label>
                    <input type="text" class="form-control" name="since_year" placeholder="e.g. 1975">
                  </div>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-12">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Purpose</label>
                    <input type="text" class="form-control" name="purpose" placeholder="e.g. FOR APPLICATION OF PROBATION">
                  </div>
                </div>
              </div>
              @endif
              
              @if($type == 'residency')
              <div class="row mb-3">
                <div class="col-md-12">
                  <div class="input-group input-group-outline mb-3">
                    <label class="form-label">Purpose</label>
                    <input type="text" class="form-control" name="purpose" placeholder="e.g. FOR IDENTIFICATION">
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
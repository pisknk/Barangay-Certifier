@extends('layouts.tenant')

@section('title', 'Certificate Generated')

@section('content')
<div class="container-fluid py-2">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3 px-3">
            <h6 class="text-white text-capitalize ps-3">Certificate Generated Successfully</h6>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="px-4 text-center">
            <div class="alert alert-success">
              <span class="material-symbols-rounded me-2">check_circle</span>
              <span>Your certificate has been generated successfully!</span>
            </div>
            
            <div class="mb-5">
              <p>What would you like to do with your certificate?</p>
              
              <div class="row justify-content-center">
                <div class="col-md-4">
                  <a href="{{ route('tenant.certificates.download', $filename) }}" class="btn bg-gradient-primary w-100 mb-3">
                    <span class="material-symbols-rounded me-2">download</span> Download Certificate
                  </a>
                </div>
                <div class="col-md-4">
                  <a href="{{ route('tenant.certificates.view', $filename) }}" class="btn bg-gradient-info w-100 mb-3" target="_blank">
                    <span class="material-symbols-rounded me-2">visibility</span> View Certificate
                  </a>
                </div>
              </div>
              
              <div class="mt-4">
                <a href="{{ route('tenant.certificates.index') }}" class="btn btn-outline-secondary">
                  <span class="material-symbols-rounded me-2">arrow_back</span> Back to Certificates
                </a>
              </div>
            </div>
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

@push('styles')
<style>
  .material-symbols-rounded {
    vertical-align: middle;
    line-height: 1;
    font-size: 20px;
  }
  
  .btn .material-symbols-rounded {
    position: relative;
    top: -1px;
  }
  
  .alert .material-symbols-rounded {
    font-size: 24px;
  }
</style>
@endpush 
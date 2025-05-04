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
                <div class="col-md-4">
                  <button type="button" class="btn bg-gradient-success w-100 mb-3" data-bs-toggle="modal" data-bs-target="#emailCertificateModal">
                    <span class="material-symbols-rounded me-2">mail</span> Send to Resident Email
                  </button>
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
  
  <!-- Email Certificate Modal -->
  <div class="modal fade" id="emailCertificateModal" tabindex="-1" aria-labelledby="emailCertificateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="sendEmailForm" action="{{ route('tenant.certificates.email', $filename) }}" method="POST">
          @csrf
          <div class="modal-header">
            <h5 class="modal-title" id="emailCertificateModalLabel">Send Certificate to Resident</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info">
              <small>The certificate will be sent as a PDF attachment to the provided email address.</small>
            </div>
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="resident_email" name="resident_email" placeholder="name@example.com" required>
              <label for="resident_email">Resident Email Address</label>
            </div>
            <div class="form-floating">
              <textarea class="form-control" id="email_message" name="email_message" placeholder="Optional message" style="height: 100px"></textarea>
              <label for="email_message">Additional Message (Optional)</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn bg-gradient-success" id="sendEmailBtn">
              <span class="material-symbols-rounded me-1">send</span> Send Email
            </button>
          </div>
        </form>
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

<!-- Toast for email status -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="emailToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-success text-white">
      <strong class="me-auto">Email Status</strong>
      <button type="button" class="btn-close text-white" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="toastMessage">
      Certificate has been sent successfully!
    </div>
  </div>
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
  
  .modal-body .form-floating {
    margin-bottom: 1rem;
  }
  
  .modal-body .form-control {
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
  }
  
  .modal-body .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  }
</style>
@endpush

@push('scripts')
<script>
  // Handle form submission via AJAX
  document.addEventListener('DOMContentLoaded', function() {
    const sendEmailForm = document.getElementById('sendEmailForm');
    const emailToast = document.getElementById('emailToast');
    const toastMessage = document.getElementById('toastMessage');
    const sendEmailBtn = document.getElementById('sendEmailBtn');
    
    if (sendEmailForm) {
      sendEmailForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        sendEmailBtn.disabled = true;
        sendEmailBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
        
        fetch(sendEmailForm.action, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({
            resident_email: document.getElementById('resident_email').value,
            email_message: document.getElementById('email_message').value
          })
        })
        .then(response => response.json())
        .then(data => {
          // Reset button state
          sendEmailBtn.disabled = false;
          sendEmailBtn.innerHTML = '<span class="material-symbols-rounded me-1">send</span> Send Email';
          
          // Close modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('emailCertificateModal'));
          modal.hide();
          
          // Show toast with message
          const toast = new bootstrap.Toast(emailToast);
          if (data.success) {
            emailToast.querySelector('.toast-header').classList.remove('bg-danger');
            emailToast.querySelector('.toast-header').classList.add('bg-success');
            toastMessage.textContent = data.message || 'Certificate has been sent successfully!';
          } else {
            emailToast.querySelector('.toast-header').classList.remove('bg-success');
            emailToast.querySelector('.toast-header').classList.add('bg-danger');
            toastMessage.textContent = data.message || 'Failed to send certificate. Please try again.';
          }
          toast.show();
        })
        .catch(error => {
          console.error('Error:', error);
          
          // Reset button state
          sendEmailBtn.disabled = false;
          sendEmailBtn.innerHTML = '<span class="material-symbols-rounded me-1">send</span> Send Email';
          
          // Show error toast
          const toast = new bootstrap.Toast(emailToast);
          emailToast.querySelector('.toast-header').classList.remove('bg-success');
          emailToast.querySelector('.toast-header').classList.add('bg-danger');
          toastMessage.textContent = 'An error occurred. Please try again.';
          toast.show();
        });
      });
    }
  });
</script>
@endpush 
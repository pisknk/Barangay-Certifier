@extends('layouts.admin', ['hideNavbar' => true])

@section('title', 'Edit Tenant: ' . ($tenant->name ?? 'Tenant'))

@section('content')

<main class="main-content mt-0">
  <!-- Add floating back button with SVG icon -->
  <a href="{{ route('admin.tenants') }}" class="position-fixed top-0 end-0 m-4 btn btn-white d-flex align-items-center justify-content-center rounded-circle z-index-3" style="width: 40px; height: 40px;">
    <img src="https://www.svgrepo.com/show/305142/arrow-ios-back.svg" alt="Back" width="20" height="20">
  </a>
  
  <!-- Toast Notification -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header bg-success text-white">
        <strong class="me-auto">Success</strong>
        <small>just now</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body" id="toastMessage">
        Operation completed successfully.
      </div>
    </div>
  </div>
  
  <!-- Confirmation Modal -->
  <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmModalLabel">Confirmation</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="confirmModalBody">
          Are you sure you want to proceed?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="confirmModalAction">Confirm</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Error Modal -->
  <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="errorModalLabel">Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="errorModalBody">
          An error occurred. Please try again.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
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
                  Edit Barangay {{ $tenant->barangay ?? '[name of tenant barangay]' }}
                </h4>
                <p class="text-white text-center mb-2">
                  You can edit the following fields:
                </p>
              </div>
            </div>
            <div class="card-body">
              <form id="updateForm" role="form" class="text-start" method="POST" action="{{ route('admin.tenants.update', $tenant->id ?? 1) }}">
                @csrf
                @method('PUT')
              <div class="row">
                <!-- Left side: Form inputs -->
                <div class="col-md-6">
                    <div class="input-group input-group-outline my-3 {{ old('name', $tenant->name ?? '') ? 'is-filled' : '' }}">
                      <label class="form-label">Full Name</label>
                      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tenant->name ?? '') }}" />
                    </div>
                    @error('name')
                    <div class="text-danger text-xs">{{ $message }}</div>
                    @enderror
                    
                    <div class="input-group input-group-outline my-3 {{ old('email', $tenant->email ?? '') ? 'is-filled' : '' }}">
                      <label class="form-label">Email</label>
                      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $tenant->email ?? '') }}" />
                    </div>
                    @error('email')
                    <div class="text-danger text-xs">{{ $message }}</div>
                    @enderror
                    
                    <div class="input-group input-group-outline mb-3 is-filled">
                      <label class="form-label">Barangay</label>
                      <input type="text" name="domain" class="form-control" value="{{ $tenant->barangay ?? '' }}" disabled />
                    </div>
                    <p class="mt-4 text-sm">
                      <b>☹️ You can't Edit Barangay</b><br />
                      This field is linked to your domain, and changing this may cause unwanted issues (such as unreachable domain, 404 Errors, and even loss of data). It's much better if left unchanged. <br><br> <b>🤔 Made a mistake or typo?</b> <br> Scroll down to learn how to remediate this.</p>
                    </p>
                    <br>
                    <hr class="dark horizontal my-0" />
                    
                    <form id="toggleForm" class="mt-4">
                    @csrf
                    <div class="text-center">
                      @php
                      $isActive = isset($tenant->is_active) ? (int)$tenant->is_active : 0;
                      $statusClasses = [
                        0 => 'bg-secondary', // Inactive
                        1 => 'bg-success',   // Active
                        2 => 'bg-warning',   // Deactivated by admin
                        3 => 'bg-danger'     // Expired
                      ];
                      $statusLabels = [
                        0 => 'Inactive',
                        1 => 'Active',
                        2 => 'Deactivated',
                        3 => 'Expired'
                      ];
                      
                      $buttonClass = $isActive === 1 ? 'bg-gradient-warning' : 'bg-gradient-success';
                      $buttonText = $isActive === 1 ? 'Deactivate' : 'Activate';
                      
                      // Expired tenants will be reactivated when subscription is renewed
                      if ($isActive === 3) {
                        $buttonText = 'Renew & Activate';
                      }
                      @endphp
                      <button
                          type="button"
                          id="toggleButton"
                          class="btn {{ $buttonClass }} w-100 mb-2"
                          onclick="console.log('Toggle button clicked'); toggleTenantStatus();"
                      >
                        {{ $buttonText }} Tenant
                      </button>
                        
                        <!-- Fallback links if JavaScript fails -->
                        <div class="mt-2 small text-center">
                          <a href="{{ url('/api/tenants/' . ($tenant->id ?? 1) . '/activate') }}" class="text-muted d-none" id="activateLink">Activate</a>
                          <a href="{{ url('/api/tenants/' . ($tenant->id ?? 1) . '/deactivate') }}" class="text-muted d-none" id="deactivateLink">Deactivate</a>
                          
                          <p class="text-muted mt-2">Tenant ID: {{ $tenant->id ?? 'Unknown' }} | 
                          Status: <span class="badge {{ $statusClasses[$isActive] ?? 'bg-secondary' }}">
                            {{ $statusLabels[$isActive] ?? 'Unknown' }}
                          </span></p>
                        </div>
                    </div>
                  </form>
                    
                    <!-- Inline script to ensure function is available -->
                    <script>
                      function toggleTenantStatus() {
                        const tenantId = '{{ $tenant->id ?? 1 }}';
                        const isActive = {{ $isActive }};
                        
                        // Use absolute URLs to the main domain for API endpoints
                        const baseUrl = 'http://localhost:8000';
                        const endpoint = isActive === 1 
                          ? `${baseUrl}/api/tenants/${tenantId}/deactivate` 
                          : `${baseUrl}/api/tenants/${tenantId}/activate`;
                        
                        console.log("Toggling tenant status:", { tenantId, isActive, endpoint });
                        
                        // Get CSRF token from meta tag
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        console.log("Using CSRF token:", csrfToken);
                        
                        // Disable button and show loading state
                        const button = document.getElementById('toggleButton');
                        const originalText = button.textContent;
                        button.disabled = true;
                        button.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;
                        
                        // Direct fetch with proper error handling
                        fetch(endpoint, {
                          method: 'PATCH', // Using PATCH for the API endpoints
                          headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                          },
                          // Add empty body for PATCH requests that may require it
                          body: JSON.stringify({})
                        })
                        .then(response => {
                          console.log("Response received:", response.status);
                          if (!response.ok) {
                            return response.text().then(text => {
                              console.error("Error response:", text);
                              throw new Error(`HTTP error! Status: ${response.status}, Body: ${text}`);
                            });
                          }
                          return response.json();
                        })
                        .then(data => {
                          console.log("Success data:", data);
                          
                          // Show message directly (fallback if toast doesn't work)
                          const message = data.message || `Tenant ${isActive === 1 ? 'deactivated' : 'activated'} successfully`;
                          console.log("Success message:", message);
                          
                          // Show as alert if toast fails
                          try {
                            document.getElementById('toastMessage').textContent = message;
                            const toastElement = document.getElementById('successToast');
                            const toast = new bootstrap.Toast(toastElement);
                            toast.show();
                          } catch (e) {
                            console.error("Error showing toast:", e);
                            alert(message);
                          }
                          
                          // Reload the page after a short delay
                          console.log("Reloading page in 1.5 seconds...");
                          setTimeout(() => {
                            window.location.reload();
                          }, 1500);
                        })
                        .catch(error => {
                          console.error('Toggle status error:', error);
                          
                          // Show error both in UI and as fallback alert
                          const errorMessage = error.message || 'An error occurred while updating tenant status';
                          
                          try {
                            document.getElementById('errorModalBody').textContent = errorMessage;
                            const errorModalElement = document.getElementById('errorModal');
                            const errorModal = new bootstrap.Modal(errorModalElement);
                            errorModal.show();
                          } catch (e) {
                            console.error("Error showing modal:", e);
                            alert(`Error: ${errorMessage}`);
                          }
                          
                          // Reset button state
                          button.disabled = false;
                          button.textContent = originalText;
                        });
                      }
                      
                      // Add direct click handler when DOM is ready
                      document.addEventListener('DOMContentLoaded', function() {
                        const toggleButton = document.getElementById('toggleButton');
                        if (toggleButton) {
                          console.log("Adding direct click handler to toggle button");
                          toggleButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            console.log("Toggle button clicked directly");
                            toggleTenantStatus();
                          });
                        } else {
                          console.error("Toggle button not found in DOM");
                        }
                      });
                    </script>
                    
                    <p class="mt-2 text-sm text-center">
                    Although this process is automatic, you can Deactivate a Tenant if they are no longer paying the subscription or they breached the contract.
                    @if($isActive === 2)
                    <br><br><span class="fw-bold text-success">Note:</span> When reactivating from "Disabled by Admin" status, the expiration date will be preserved.
                    @endif
                  </p>
                    <hr class="dark horizontal my-0" />
                    
                </div>

                <!-- Right side: Plan picker -->
                <div class="col-md-6">
                  <br />
                  <h5 class="text-center mb-1">Choose a Plan</h5>
                  @if($tenant->subscription_plan)
                  <p class="text-center text-sm mb-4">
                    Current plan: <span class="fw-bold">{{ $tenant->subscription_plan }}</span>
                    @if($tenant->valid_until)
                      <br>Expires: <span class="fw-bold">{{ date('M d, Y', strtotime($tenant->valid_until)) }}</span>
                    @endif
                  </p>
                  @endif
                    
                    <!-- Basic Plan -->
                    <div class="card mb-3 {{ str_contains(strtolower($tenant->subscription_plan ?? ''), 'basic') ? 'border border-success shadow-sm' : '' }}">
                      <div class="card-body">
                        <div class="form-check mb-2">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="subscription_plan"
                            id="basic"
                            value="Basic P399"
                            {{ !$tenant->subscription_plan || str_contains(strtolower($tenant->subscription_plan ?? ''), 'basic') ? 'checked' : '' }}
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
                    <div class="card mb-3 {{ str_contains(strtolower($tenant->subscription_plan ?? ''), 'essentials') ? 'border border-success shadow-sm' : '' }}">
                      <div class="card-body">
                        <div class="form-check mb-2">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="subscription_plan"
                            id="essentials"
                            value="Essentials P799"
                            {{ str_contains(strtolower($tenant->subscription_plan ?? ''), 'essentials') ? 'checked' : '' }}
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
                    <div class="card mb-3 {{ str_contains(strtolower($tenant->subscription_plan ?? ''), 'ultimate') ? 'border border-success shadow-sm' : '' }}">
                      <div class="card-body">
                        <div class="form-check mb-2">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="subscription_plan"
                            id="ultimate"
                            value="Ultimate P1299"
                            {{ str_contains(strtolower($tenant->subscription_plan ?? ''), 'ultimate') ? 'checked' : '' }}
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

                <div class="row justify-content-center mt-4">
                  <div class="col-md-6">
                    <div class="text-center">
                      <button
                        type="button"
                        id="updateButton"
                        class="btn bg-gradient-dark w-100 mb-2"
                        onclick="submitForm()"
                      >
                        Update Tenant and Plan
                      </button>
                    </div>
                  </div>
                </div>
              </form>
              
              <!-- Basic direct script for form submission -->
              <script>
                // Console log when the page loads to verify script execution
                console.log("Script loaded on tenant edit page");
                
                function submitForm() {
                  try {
                    console.log("Submit function called");
                    // Get the form element
                    const form = document.getElementById('updateForm');
                    console.log("Form found:", form);
                    
                    // Show confirmation modal
                    document.getElementById('confirmModalLabel').textContent = 'Update Tenant';
                    document.getElementById('confirmModalBody').textContent = 'Are you sure you want to update this tenant and their subscription plan?';
                    
                    // Set up the confirm button action
                    const confirmButton = document.getElementById('confirmModalAction');
                    confirmButton.onclick = function() {
                      // Hide the modal
                      const modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
                      modal.hide();
                      
                      // Submit the form
                      form.submit();
                    };
                    
                    // Show the modal
                    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                    confirmModal.show();
                  } catch (error) {
                    console.error("Error in form submission:", error);
                    // Show error modal
                    document.getElementById('errorModalBody').textContent = error.message || 'An error occurred while preparing the form';
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                  }
                }
                
                // Add a DOMContentLoaded event to ensure scripts run after the page loads
                document.addEventListener('DOMContentLoaded', function() {
                  console.log("DOM fully loaded");
                  
                  // Try to find the form and button
                  const form = document.getElementById('updateForm');
                  const button = document.getElementById('updateButton');
                  
                  console.log("Form found on page load:", form);
                  console.log("Button found on page load:", button);
                  
                  // Check for success message in session
                  @if(session('success'))
                  document.getElementById('toastMessage').textContent = "{{ session('success') }}";
                  const toast = new bootstrap.Toast(document.getElementById('successToast'));
                  toast.show();
                  @endif
                });
              </script>
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
                href="#"
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
@endsection

@push('scripts')
<script>
  // Initialize Bootstrap components
  document.addEventListener('DOMContentLoaded', function() {
    console.log("Initializing Bootstrap components");
    
    // Initialize toasts
    var toastElements = [].slice.call(document.querySelectorAll('.toast'));
    toastElements.map(function(toastElement) {
      try {
        return new bootstrap.Toast(toastElement, { 
          autohide: true,
          delay: 3000
        });
      } catch (e) {
        console.error("Error initializing toast:", e);
        return null;
      }
    });
    
    // Initialize modals
    var modalElements = [].slice.call(document.querySelectorAll('.modal'));
    modalElements.map(function(modalElement) {
      try {
        return new bootstrap.Modal(modalElement);
      } catch (e) {
        console.error("Error initializing modal:", e);
        return null;
      }
    });
    
    // Test if bootstrap is available
    console.log("Bootstrap available:", typeof bootstrap !== 'undefined');
  });
</script>
@endpush

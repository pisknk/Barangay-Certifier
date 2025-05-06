@extends('layouts.tenant')

@section('title', 'Settings')

@section('content')
<div class="container-fluid py-4">
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
  
  @if(session('info'))
  <div class="alert alert-info alert-dismissible fade show" role="alert">
    {{ session('info') }}
    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
            <h6 class="text-white text-capitalize ps-3">Settings</h6>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="container">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-3" id="settingsTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="certificate-tab" data-bs-toggle="tab" data-bs-target="#certificate" type="button" role="tab" aria-controls="certificate" aria-selected="true">Certificate Options</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="website-tab" data-bs-toggle="tab" data-bs-target="#website" type="button" role="tab" aria-controls="website" aria-selected="false">Website Settings</button>
              </li>
            </ul>
            
            <!-- Define subscription plan variables -->
            @php
              // Get current tenant using the tenant() helper
              $currentTenant = tenant();
              $plan = $currentTenant->subscription_plan ?? 'Basic P399';
              
              // Feature flags - using case-insensitive matching
              $planLower = strtolower($plan);
              $canCustomizeTheme = (stripos($planLower, 'ultimate') !== false);
              $canCustomizePaperSize = (stripos($planLower, 'essentials') !== false || stripos($planLower, 'ultimate') !== false);
              $canGetSoftwareUpdates = (stripos($planLower, 'ultimate') !== false);
            @endphp
            
            <!-- Tab content -->
            <div class="tab-content mt-4">
              <!-- Certificate Options Tab -->
              <div class="tab-pane fade show active" id="certificate" role="tabpanel" aria-labelledby="certificate-tab">
                <form action="{{ route('tenant.settings.update-certificate') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  
                  <div class="row mb-4">
                    <div class="col-md-6">
                      <div class="card">
                        <div class="card-header pb-0 p-3">
                          <h6 class="mb-0">Barangay Logo</h6>
                        </div>
                        <div class="card-body p-3">
                          <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-xl position-relative">
                              <img src="{{ $viewSettings['barangay_logo'] }}" alt="Barangay Logo" class="w-100 border-radius-lg shadow-sm" id="preview-barangay-logo" style="max-width: 150px; max-height: 150px;">
                            </div>
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label" for="barangay_logo">Upload New Logo</label>
                            <input type="file" class="form-control" id="barangay_logo" name="barangay_logo" accept="image/*">
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label" for="barangay_logo_url">Or Enter Image URL</label>
                            <input type="url" class="form-control" id="barangay_logo_url" name="barangay_logo_url" placeholder="https://example.com/logo.png">
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="card">
                        <div class="card-header pb-0 p-3">
                          <h6 class="mb-0">Municipality Logo</h6>
                        </div>
                        <div class="card-body p-3">
                          <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-xl position-relative">
                              <img src="{{ $viewSettings['municipality_logo'] }}" alt="Municipality Logo" class="w-100 border-radius-lg shadow-sm" id="preview-municipality-logo" style="max-width: 150px; max-height: 150px;">
                            </div>
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label" for="municipality_logo">Upload New Logo</label>
                            <input type="file" class="form-control" id="municipality_logo" name="municipality_logo" accept="image/*">
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label" for="municipality_logo_url">Or Enter Image URL</label>
                            <input type="url" class="form-control" id="municipality_logo_url" name="municipality_logo_url" placeholder="https://example.com/logo.png">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card">
                        <div class="card-header pb-0 p-3">
                          <h6 class="mb-0">Other Certificate Settings</h6>
                        </div>
                        <div class="card-body p-3">
                          <div class="mb-3">
                            <label class="form-label font-weight-bold">Certificate Header Structure</label>
                            <div class="p-3 bg-light border rounded mb-2">
                              <p class="mb-0"><strong>Republic of the Philippines</strong> (Fixed)</p>
                            </div>
                          </div>

                          <div class="mb-3">
                            <label class="form-label" for="province">Province</label>
                            <div class="input-group mb-3">
                              <span class="input-group-text">Province of</span>
                              <input type="text" class="form-control" id="province" name="province" value="{{ $viewSettings['province'] ?? '' }}" placeholder="Enter province name">
                            </div>
                          </div>
                          
                          <div class="row mb-3">
                            <div class="col-md-4">
                              <label class="form-label" for="municipality_type">Municipality Type</label>
                              <select class="form-select" id="municipality_type" name="municipality_type">
                                <option value="Municipality" {{ ($viewSettings['municipality_type'] ?? '') == 'Municipality' ? 'selected' : '' }}>Municipality</option>
                                <option value="City" {{ ($viewSettings['municipality_type'] ?? '') == 'City' ? 'selected' : '' }}>City</option>
                              </select>
                            </div>
                            <div class="col-md-8">
                              <label class="form-label" for="municipality">Municipality/City Name</label>
                              <input type="text" class="form-control" id="municipality" name="municipality" value="{{ $viewSettings['municipality'] ?? '' }}" placeholder="Enter municipality/city name">
                            </div>
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label">Barangay</label>
                            <div class="p-3 bg-light border rounded mb-2">
                              <p class="mb-0">Barangay {{ tenant()->barangay }}</p>
                              <small class="text-muted">This comes from your tenant registration.</small>
                            </div>
                          </div>
                          
                          <div class="mb-3">
                            <label class="form-label" for="punong_barangay">Punong Barangay</label>
                            <input type="text" class="form-control" id="punong_barangay" name="punong_barangay" value="{{ $viewSettings['punong_barangay'] ?? '' }}" placeholder="Enter Punong Barangay name">
                            <small class="text-muted">This name will appear in the certificate signature block.</small>
                          </div>
                          
                          <div class="alert alert-info" role="alert">
                            <span class="material-symbols-rounded me-2">info</span>
                            <span>The certificate header will appear as:<br>
                            <strong>Republic of the Philippines<br>
                            Province of [Your Province]<br>
                            [Municipality/City] of [Your Municipality/City]<br>
                            Barangay {{ tenant()->barangay }}</strong></span>
                          </div>
                          
                          <input type="hidden" id="header" name="header" value="{{ $viewSettings['header'] }}">
                          
                          <div class="row">
                            <div class="col-md-6">
                              <div class="mb-3">
                                <label class="form-label" for="paper_size">Default Paper Size</label>
                                <select class="form-select" id="paper_size" name="paper_size" required {{ $canCustomizePaperSize ? '' : 'disabled' }}>
                                  <option value="A4" {{ $viewSettings['paper_size'] == 'A4' ? 'selected' : '' }}>A4</option>
                                  <option value="Letter" {{ $viewSettings['paper_size'] == 'Letter' ? 'selected' : '' }}>Letter</option>
                                  <option value="Legal" {{ $viewSettings['paper_size'] == 'Legal' ? 'selected' : '' }}>Legal</option>
                                </select>
                                @if(!$canCustomizePaperSize)
                                <small class="text-muted d-block mt-1">
                                  <span class="material-symbols-rounded text-warning align-middle" style="font-size: 16px;">lock</span>
                                  Upgrade to Essentials or Ultimate plan to customize paper size
                                </small>
                                @endif
                              </div>
                            </div>
                          </div>
                          
                          <div class="text-end">
                            <button type="submit" class="btn bg-gradient-dark mb-0">Save Certificate Settings</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              
              <!-- Website Settings Tab -->
              <div class="tab-pane fade" id="website" role="tabpanel" aria-labelledby="website-tab">
                <div class="row mb-4">
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Subscription Details</h6>
                      </div>
                      <div class="card-body p-3">
                        <div class="info-list">
                          <div class="d-flex justify-content-between mb-2">
                            <span class="text-dark font-weight-bold">Plan:</span>
                            <span>{{ $viewSettings['subscription_plan'] }}</span>
                          </div>
                          
                          <div class="d-flex justify-content-between mb-2">
                            <span class="text-dark font-weight-bold">Expiry Date:</span>
                            <span>{{ $viewSettings['subscription_expiry'] }}</span>
                          </div>
                          
                          <div class="d-flex justify-content-between mb-2">
                            <span class="text-dark font-weight-bold">Purchased By:</span>
                            <span>{{ $viewSettings['subscription_owner'] }}</span>
                          </div>
                          
                          <div class="d-flex justify-content-between mb-2">
                            <span class="text-dark font-weight-bold">Purchase Date:</span>
                            <span>{{ $viewSettings['subscription_purchased_at'] }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Software Updates</h6>
                      </div>
                      <div class="card-body p-3">
                        <p>Check for the latest version of Barangay Certifier.</p>
                        
                        <!-- Container for update info - hidden by default -->
                        <div id="update-info-container" class="mb-3" style="display:none;">
                          <div class="alert alert-info">
                            <h6 class="alert-heading font-weight-bold">Update Available</h6>
                            <div id="update-info">
                              <p class="mb-1"><strong>Current Version:</strong> <span id="current-version">-</span></p>
                              <p class="mb-1"><strong>New Version:</strong> <span id="new-version">-</span></p>
                              <p class="mb-1"><strong>Release Name:</strong> <span id="version-name">-</span></p>
                              <div class="mt-2">
                                <p class="mb-1"><strong>Release Notes:</strong></p>
                                <div id="release-notes" class="p-2 bg-light rounded" style="max-height: 150px; overflow-y: auto;">
                                  -
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Update progress container - hidden by default -->
                        <div id="update-progress-container" class="mb-3" style="display:none;">
                          <h6 class="font-weight-bold">Update in Progress</h6>
                          <div class="progress mb-2">
                            <div id="update-progress-bar" class="progress-bar bg-gradient-info" role="progressbar" 
                                 style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                          </div>
                          <p class="text-sm" id="update-status">Preparing to update...</p>
                          
                          <!-- Log output container -->
                          <div class="mt-3">
                            <h6 class="font-weight-bold">Update Log</h6>
                            <div id="update-log" class="p-2 bg-dark text-white rounded" style="max-height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                              Waiting for update to start...
                            </div>
                          </div>
                        </div>
                        
                        <div class="text-center">
                          <!-- Check for updates button -->
                          <form id="check-updates-form" action="{{ route('tenant.settings.check-updates') }}" method="POST">
                            @csrf
                            <button type="submit" id="check-updates-btn" class="btn bg-gradient-info mb-0" {{ $canGetSoftwareUpdates ? '' : 'disabled' }}>
                              <i class="material-symbols-rounded me-2">update</i>
                              Check for Updates
                            </button>
                          </form>
                          
                          <!-- Update now button - hidden by default -->
                          <button id="update-now-btn" class="btn bg-gradient-success mb-0 mt-2" style="display:none;">
                            <i class="material-symbols-rounded me-2">system_update</i>
                            Update Now
                          </button>
                            
                          @if(!$canGetSoftwareUpdates)
                          <div class="mt-2 text-muted">
                            <span class="material-symbols-rounded text-warning align-middle" style="font-size: 16px;">lock</span>
                            Upgrade to Ultimate plan to access software updates
                          </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Theme settings card - only for Ultimate plan -->
                <div class="row mb-4">
                  <div class="col-md-12">
                    <div class="card">
                      <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Theme Settings</h6>
                      </div>
                      <div class="card-body p-3">
                        @if($canCustomizeTheme)
                        <p>Click the "Customize" button to open the theme customizer panel where you can change colors, layout, and more.</p>
                        <div class="text-center">
                          <button type="button" id="open-theme-settings" class="btn bg-gradient-primary mb-0">
                            <i class="material-symbols-rounded me-2">palette</i>
                            Open Theme Customizer
                          </button>
                        </div>
                        @else
                        <div class="alert alert-warning" role="alert">
                          <span class="material-symbols-rounded me-2">lock</span>
                          <strong>Feature Locked:</strong> Theme customization is only available on the Ultimate plan. Upgrade your subscription to customize the dashboard appearance.
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Subscription plan comparison -->
                <div class="row mb-4">
                  <div class="col-md-12">
                    <div class="card">
                      <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Plan Features Comparison</h6>
                      </div>
                      <div class="card-body p-3">
                        <div class="table-responsive">
                          <table class="table align-items-center mb-0">
                            <thead>
                              <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Feature</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Basic</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Essentials</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ultimate</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>
                                  <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                      <h6 class="mb-0 text-sm">Custom Page Headers</h6>
                                    </div>
                                  </div>
                                </td>
                                <td><span class="text-success text-sm font-weight-bold"><i class="material-symbols-rounded">check</i></span></td>
                                <td><span class="text-success text-sm font-weight-bold"><i class="material-symbols-rounded">check</i></span></td>
                                <td><span class="text-success text-sm font-weight-bold"><i class="material-symbols-rounded">check</i></span></td>
                              </tr>
                              <tr>
                                <td>
                                  <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                      <h6 class="mb-0 text-sm">Custom Paper Size</h6>
                                    </div>
                                  </div>
                                </td>
                                <td><span class="text-danger text-sm font-weight-bold"><i class="material-symbols-rounded">close</i></span></td>
                                <td><span class="text-success text-sm font-weight-bold"><i class="material-symbols-rounded">check</i></span></td>
                                <td><span class="text-success text-sm font-weight-bold"><i class="material-symbols-rounded">check</i></span></td>
                              </tr>
                              <tr>
                                <td>
                                  <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                      <h6 class="mb-0 text-sm">Software Updates</h6>
                                    </div>
                                  </div>
                                </td>
                                <td><span class="text-danger text-sm font-weight-bold"><i class="material-symbols-rounded">close</i></span></td>
                                <td><span class="text-danger text-sm font-weight-bold"><i class="material-symbols-rounded">close</i></span></td>
                                <td><span class="text-success text-sm font-weight-bold"><i class="material-symbols-rounded">check</i></span></td>
                              </tr>
                              <tr>
                                <td>
                                  <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                      <h6 class="mb-0 text-sm">Customizable Theme</h6>
                                    </div>
                                  </div>
                                </td>
                                <td><span class="text-danger text-sm font-weight-bold"><i class="material-symbols-rounded">close</i></span></td>
                                <td><span class="text-danger text-sm font-weight-bold"><i class="material-symbols-rounded">close</i></span></td>
                                <td><span class="text-success text-sm font-weight-bold"><i class="material-symbols-rounded">check</i></span></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="mt-4 text-center">
                          <p>Please contact the system administrator to upgrade your subscription plan.</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
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

<!-- Update Confirmation Modal -->
<div class="modal fade" id="updateConfirmModal" tabindex="-1" role="dialog" aria-labelledby="updateConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateConfirmModalLabel">Confirm System Update</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to update the system now?</p>
        <div class="alert alert-warning">
          <i class="material-symbols-rounded me-2">warning</i>
          This will temporarily make the application unavailable while the update is in progress.
        </div>
        <p class="mb-0">Make sure you have backed up your data before proceeding.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn bg-gradient-success" id="confirm-update-btn">
          <i class="material-symbols-rounded me-2">system_update</i>
          Update Now
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .form-control, .form-select {
    background-color: #fff !important;
    border: 1px solid #d2d6da !important;
    padding: 0.5rem 0.75rem !important;
    height: auto !important;
    font-size: 14px;
  }
  
  .form-label {
    color: #344767;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 8px;
    display: inline-block;
  }
  
  /* Improved tab styling */
  .nav-tabs {
    border-bottom: 1px solid #dee2e6;
  }
  
  .nav-tabs .nav-link {
    margin-bottom: -1px;
    border: 1px solid transparent;
    border-top-left-radius: 0.25rem;
    border-top-right-radius: 0.25rem;
    color: #344767;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    transition: all 0.15s ease-in;
  }
  
  .nav-tabs .nav-link:hover {
    border-color: #e9ecef #e9ecef #dee2e6;
    isolation: isolate;
  }
  
  .nav-tabs .nav-link.active {
    color: #344767;
    font-weight: 600;
    background-color: #fff;
    border-color: #dee2e6 #dee2e6 #fff;
  }
</style>
@endpush

@push('scripts')
<script>
  // Preview uploaded images
  document.getElementById('barangay_logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(event) {
        document.getElementById('preview-barangay-logo').src = event.target.result;
      }
      reader.readAsDataURL(file);
    }
  });
  
  document.getElementById('municipality_logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(event) {
        document.getElementById('preview-municipality-logo').src = event.target.result;
      }
      reader.readAsDataURL(file);
    }
  });
  
  // URL input handlers
  document.getElementById('barangay_logo_url').addEventListener('blur', function(e) {
    const url = e.target.value.trim();
    if (url) {
      document.getElementById('preview-barangay-logo').src = url;
    }
  });
  
  document.getElementById('municipality_logo_url').addEventListener('blur', function(e) {
    const url = e.target.value.trim();
    if (url) {
      document.getElementById('preview-municipality-logo').src = url;
    }
  });
  
  // Theme settings button handler
  const openThemeSettingsBtn = document.getElementById('open-theme-settings');
  if (openThemeSettingsBtn) {
    openThemeSettingsBtn.addEventListener('click', function() {
      // Try to find the settings button in different ways
      let themeSettingsBtn = document.getElementById('theme-settings-button');
      
      if (themeSettingsBtn) {
        // Click the button to open the theme settings panel
        themeSettingsBtn.click();
      } else {
        // If the button is not found, try to find the fixed-plugin element and make it visible
        const fixedPlugin = document.querySelector('.fixed-plugin');
        if (fixedPlugin) {
          const pluginCard = fixedPlugin.querySelector('.card');
          if (pluginCard) {
            pluginCard.classList.add('show');
          }
        } else {
          // If all else fails, alert the user
          alert('Theme customizer is not available. Please refresh the page and try again.');
        }
      }
    });
  }
  
  // Software update functionality
  document.addEventListener('DOMContentLoaded', function() {
    // DOM elements
    const checkUpdatesForm = document.getElementById('check-updates-form');
    const checkUpdatesBtn = document.getElementById('check-updates-btn');
    const updateNowBtn = document.getElementById('update-now-btn');
    const updateInfoContainer = document.getElementById('update-info-container');
    const updateProgressContainer = document.getElementById('update-progress-container');
    const updateProgressBar = document.getElementById('update-progress-bar');
    const updateStatus = document.getElementById('update-status');
    const updateLog = document.getElementById('update-log');
    
    // Elements for update info
    const currentVersionEl = document.getElementById('current-version');
    const newVersionEl = document.getElementById('new-version');
    const versionNameEl = document.getElementById('version-name');
    const releaseNotesEl = document.getElementById('release-notes');
    
    // Variables to track update state
    let updateInProgress = false;
    let updatePollingInterval = null;
    
    // Check if there's already an update available when the page loads
    checkForStoredUpdateInfo();
    
    // Check for a stored update file
    function checkForStoredUpdateInfo() {
      fetch('{{ route("tenant.settings.update-progress") }}')
        .then(response => response.json())
        .then(data => {
          // If an update is in progress, show the progress UI
          if (data.progress > 0 && data.progress < 100 && !data.error) {
            showUpdateProgress(data.progress, data.logs);
            startUpdatePolling();
          } else {
            // Check for available update
            checkStoredUpdateFile();
          }
        })
        .catch(error => {
          console.error('Error checking update progress:', error);
          // Check for available update
          checkStoredUpdateFile();
        });
    }
    
    // Check for stored update file on page load
    function checkStoredUpdateFile() {
      fetch('/api/check-update-file', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.update_available) {
          displayUpdateInfo(data.update_info);
        }
      })
      .catch(error => {
        console.error('Error checking for stored update file:', error);
      });
    }
    
    // Intercept form submission to use AJAX instead
    if (checkUpdatesForm) {
      checkUpdatesForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Disable the button and show loading state
        checkUpdatesBtn.disabled = true;
        checkUpdatesBtn.innerHTML = '<i class="material-symbols-rounded me-2">hourglass_top</i> Checking...';
        
        // Make AJAX request to check for updates
        fetch(checkUpdatesForm.action, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          },
          body: JSON.stringify({})
        })
        .then(response => {
          // Check if we got redirected
          if (response.redirected) {
            window.location.href = response.url;
            return null;
          }
          return response.json();
        })
        .then(data => {
          if (data) {
            if (data.status === 'success') {
              // Show update info
              displayUpdateInfo(data.update_info);
            } else {
              // Show error message
              alert(data.message || 'Failed to check for updates');
            }
          }
        })
        .catch(error => {
          console.error('Error checking for updates:', error);
          alert('An error occurred while checking for updates. Please try again later.');
        })
        .finally(() => {
          // Re-enable the button and reset text
          checkUpdatesBtn.disabled = false;
          checkUpdatesBtn.innerHTML = '<i class="material-symbols-rounded me-2">update</i> Check for Updates';
        });
      });
    }
    
    // Display update information
    function displayUpdateInfo(updateInfo) {
      if (!updateInfo) return;
      
      // Show update info container
      updateInfoContainer.style.display = 'block';
      
      // Fill in update details
      currentVersionEl.textContent = updateInfo.current_version || '-';
      newVersionEl.textContent = updateInfo.latest_version || '-';
      versionNameEl.textContent = updateInfo.version_name || 'Unnamed Release';
      releaseNotesEl.innerHTML = formatReleaseNotes(updateInfo.release_notes) || 'No release notes available';
      
      // Show update button
      updateNowBtn.style.display = 'inline-block';
      
      // Remove existing event listeners and add new one
      updateNowBtn.replaceWith(updateNowBtn.cloneNode(true));
      // Get the fresh reference after replacement
      const freshUpdateBtn = document.getElementById('update-now-btn');
      // Add the click handler
      freshUpdateBtn.addEventListener('click', startUpdate);
    }
    
    // Format release notes with some basic Markdown-like support
    function formatReleaseNotes(releaseNotes) {
      if (!releaseNotes) return '';
      
      // Convert GitHub flavor markdown to HTML
      let html = releaseNotes
        // Convert ** or __ to bold
        .replace(/(\*\*|__)(.*?)\1/g, '<strong>$2</strong>')
        // Convert * or _ to italic
        .replace(/(\*|_)(.*?)\1/g, '<em>$2</em>')
        // Convert ### headers
        .replace(/###(.*)/g, '<h5>$1</h5>')
        // Convert ## headers
        .replace(/##(.*)/g, '<h4>$1</h4>')
        // Convert # headers
        .replace(/#(.*)/g, '<h3>$1</h3>')
        // Convert URLs to links
        .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>')
        // Convert newlines to <br>
        .replace(/\n/g, '<br>');
        
      return html;
    }
    
    // Start the update process
    function startUpdate() {
      if (updateInProgress) return;
      
      // Show the Bootstrap modal instead of using native confirm dialog
      const updateConfirmModal = new bootstrap.Modal(document.getElementById('updateConfirmModal'));
      updateConfirmModal.show();
    }
    
    // Handle the actual update when confirmed via modal
    document.getElementById('confirm-update-btn').addEventListener('click', function() {
      // Hide the modal
      const updateConfirmModal = bootstrap.Modal.getInstance(document.getElementById('updateConfirmModal'));
      updateConfirmModal.hide();
      
      updateInProgress = true;
      
      // Hide update info and show progress UI
      updateInfoContainer.style.display = 'none';
      updateProgressContainer.style.display = 'block';
      
      // Disable update button
      updateNowBtn.disabled = true;
      updateNowBtn.innerHTML = '<i class="material-symbols-rounded me-2">hourglass_top</i> Starting...';
      
      // Make AJAX request to start the update
      fetch('{{ route("tenant.settings.perform-update") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({})
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Update initiated successfully, start polling for progress
          updateStatus.textContent = 'Update process started. Please do not close this window.';
          updateLog.innerHTML = 'Update process initiated. Starting update from ' + data.current_version + ' to ' + data.latest_version + '...\n';
          
          // Start polling for progress
          startUpdatePolling();
        } else {
          // Show error message
          updateStatus.textContent = 'Failed to start update: ' + (data.message || 'Unknown error');
          updateLog.innerHTML += 'Error: ' + (data.message || 'Unknown error') + '\n';
          updateProgressBar.classList.remove('bg-gradient-info');
          updateProgressBar.classList.add('bg-gradient-danger');
          
          // Re-enable update button after delay
          setTimeout(() => {
            updateNowBtn.disabled = false;
            updateNowBtn.innerHTML = '<i class="material-symbols-rounded me-2">system_update</i> Try Again';
            
            // Replace the button to remove any existing listeners and add new one
            updateNowBtn.replaceWith(updateNowBtn.cloneNode(true));
            const retryBtn = document.getElementById('update-now-btn');
            retryBtn.addEventListener('click', startUpdate);
          }, 3000);
        }
      })
      .catch(error => {
        console.error('Error starting update:', error);
        updateStatus.textContent = 'Error starting update: ' + error.message;
        updateLog.innerHTML += 'Error: ' + error.message + '\n';
        updateProgressBar.classList.remove('bg-gradient-info');
        updateProgressBar.classList.add('bg-gradient-danger');
        
        // Re-enable update button after delay
        setTimeout(() => {
          updateNowBtn.disabled = false;
          updateNowBtn.innerHTML = '<i class="material-symbols-rounded me-2">system_update</i> Try Again';
          
          // Replace the button to remove any existing listeners and add new one
          updateNowBtn.replaceWith(updateNowBtn.cloneNode(true));
          const retryBtn = document.getElementById('update-now-btn');
          retryBtn.addEventListener('click', startUpdate);
        }, 3000);
      });
    });
    
    // Start polling for update progress
    function startUpdatePolling() {
      if (updatePollingInterval) {
        clearInterval(updatePollingInterval);
      }
      
      // Poll every 2 seconds
      updatePollingInterval = setInterval(pollUpdateProgress, 2000);
      
      // Also poll immediately
      pollUpdateProgress();
    }
    
    // Poll for update progress
    function pollUpdateProgress() {
      fetch('{{ route("tenant.settings.update-progress") }}')
        .then(response => response.json())
        .then(data => {
          showUpdateProgress(data.progress, data.logs);
          
          // Check if update is completed or failed
          if (data.completed || data.error || data.progress >= 100) {
            clearInterval(updatePollingInterval);
            
            if (data.error) {
              // Show error state
              updateStatus.textContent = 'Update failed. See log for details.';
              updateProgressBar.classList.remove('bg-gradient-info');
              updateProgressBar.classList.add('bg-gradient-danger');
              
              // Re-enable update button to allow retry
              updateNowBtn.disabled = false;
              updateNowBtn.innerHTML = '<i class="material-symbols-rounded me-2">system_update</i> Try Again';
              
              // Replace the button to remove any existing listeners and add new one
              updateNowBtn.replaceWith(updateNowBtn.cloneNode(true));
              const retryBtn = document.getElementById('update-now-btn');
              retryBtn.addEventListener('click', startUpdate);
            } else {
              // Show success state
              updateStatus.textContent = 'Update completed successfully!';
              updateProgressBar.classList.remove('bg-gradient-info');
              updateProgressBar.classList.add('bg-gradient-success');
              
              // Show reload button
              updateNowBtn.disabled = false;
              updateNowBtn.innerHTML = '<i class="material-symbols-rounded me-2">refresh</i> Reload Application';
              updateNowBtn.classList.remove('bg-gradient-success');
              updateNowBtn.classList.add('bg-gradient-primary');
              
              // Change function of update button to reload page
              updateNowBtn.replaceWith(updateNowBtn.cloneNode(true));
              const reloadBtn = document.getElementById('update-now-btn');
              reloadBtn.addEventListener('click', function() {
                window.location.reload();
              });
            }
          }
        })
        .catch(error => {
          console.error('Error polling update progress:', error);
          updateLog.innerHTML += 'Error polling progress: ' + error.message + '\n';
          
          // If we fail to poll for too long, assume something went wrong
          if (updatePollingInterval) {
            clearInterval(updatePollingInterval);
            updateStatus.textContent = 'Lost connection to server. Update may still be in progress.';
            
            // Re-enable update button to allow checking again
            updateNowBtn.disabled = false;
            updateNowBtn.innerHTML = '<i class="material-symbols-rounded me-2">refresh</i> Check Status';
          }
        });
    }
    
    // Show update progress in the UI
    function showUpdateProgress(progress, logs) {
      // Update progress bar
      updateProgressBar.style.width = progress + '%';
      updateProgressBar.setAttribute('aria-valuenow', progress);
      
      // Update status text based on progress
      if (progress < 15) {
        updateStatus.textContent = 'Creating backup...';
      } else if (progress < 30) {
        updateStatus.textContent = 'Downloading update package...';
      } else if (progress < 45) {
        updateStatus.textContent = 'Extracting update package...';
      } else if (progress < 60) {
        updateStatus.textContent = 'Applying updates...';
      } else if (progress < 75) {
        updateStatus.textContent = 'Running migrations and clearing caches...';
      } else if (progress < 90) {
        updateStatus.textContent = 'Updating version record...';
      } else if (progress < 100) {
        updateStatus.textContent = 'Cleaning up...';
      } else {
        updateStatus.textContent = 'Update completed!';
      }
      
      // Update log output
      if (logs && logs.length > 0) {
        updateLog.innerHTML = logs.join('<br>');
        // Scroll to bottom
        updateLog.scrollTop = updateLog.scrollHeight;
      }
    }
  });
</script>
@endpush 
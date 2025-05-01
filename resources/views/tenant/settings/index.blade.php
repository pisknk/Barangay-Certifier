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
                        <div class="text-center">
                          <form action="{{ route('tenant.settings.check-updates') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn bg-gradient-info mb-0" {{ $canGetSoftwareUpdates ? '' : 'disabled' }}>
                              <i class="material-symbols-rounded me-2">update</i>
                              Check for Updates
                            </button>
                            
                            @if(!$canGetSoftwareUpdates)
                            <div class="mt-2 text-muted">
                              <span class="material-symbols-rounded text-warning align-middle" style="font-size: 16px;">lock</span>
                              Upgrade to Ultimate plan to access software updates
                            </div>
                            @endif
                          </form>
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
</script>
@endpush 
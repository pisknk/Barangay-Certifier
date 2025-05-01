@extends('layouts.admin')

@section('title', 'System Updates')

@section('content')
<div class="container-fluid py-2">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
            <h6 class="text-white text-capitalize ps-3 mb-0">System Updates</h6>
            <div class="pe-4">
              <button id="check-updates-btn" class="btn btn-sm btn-info">
                Check for Updates
              </button>
            </div>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="px-4">
            @if(session('success'))
            <div class="alert alert-success">
              {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger">
              {{ session('error') }}
            </div>
            @endif
          </div>
          
          <div class="px-4 py-2">
            <div class="version-info mb-4">
              <h5>Current Version</h5>
              <div class="d-flex align-items-center">
                <div class="bg-gradient-dark shadow-dark text-white rounded-circle p-3 me-3">
                  <i class="material-symbols-rounded">phone_iphone</i>
                </div>
                <div>
                  <h6 class="mb-0">{{ $updateInfo['current_version'] }}</h6>
                  <p class="text-sm text-muted mb-0">Installed version</p>
                </div>
              </div>
            </div>
            
            <div id="update-status">
              @if($updateInfo['has_update'])
              <div class="alert alert-info">
                <div class="d-flex align-items-center">
                  <i class="material-symbols-rounded me-2">notifications</i>
                  <div>
                    <h6 class="alert-heading mb-0">Update Available!</h6>
                    <p class="mb-0">Version {{ $updateInfo['latest_version'] }} is now available.</p>
                  </div>
                </div>
              </div>
              
              <div class="card mb-4">
                <div class="card-header">
                  <h6 class="mb-0">Version {{ $updateInfo['latest_version'] }}</h6>
                  <p class="text-sm text-muted mb-0">
                    Released: {{ \Carbon\Carbon::parse($updateInfo['published_at'])->format('M d, Y') }}
                  </p>
                </div>
                <div class="card-body">
                  <h6>Release Notes:</h6>
                  <div class="release-notes">
                    {!! nl2br(e($updateInfo['release_notes'])) !!}
                  </div>
                </div>
                <div class="card-footer">
                  <div class="d-flex justify-content-between">
                    <div>
                      <form action="{{ route('admin.updates.download') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Download Update</button>
                      </form>
                    </div>
                    
                    @if(file_exists(storage_path('app/update.zip')))
                    <div>
                      <form action="{{ route('admin.updates.install') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Install Update</button>
                      </form>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
              @else
              <div class="alert alert-success">
                <div class="d-flex align-items-center">
                  <i class="material-symbols-rounded me-2">check_circle</i>
                  <div>
                    <h6 class="alert-heading mb-0">You're up to date!</h6>
                    <p class="mb-0">
                      @if(isset($updateInfo['message']))
                        {{ $updateInfo['message'] }}
                      @else
                        Your system is running the latest version.
                      @endif
                    </p>
                  </div>
                </div>
              </div>
              @endif
            </div>
            
            <div class="mt-4">
              <h5>How Updates Work</h5>
              <p class="text-sm">
                The update system follows these steps:
              </p>
              <ol class="text-sm">
                <li>Checks GitHub for the latest release of Barangay Certifier</li>
                <li>Downloads the update package as a ZIP file</li>
                <li>Creates a backup of your current installation</li>
                <li>Extracts and installs the new version</li>
              </ol>
              <p class="text-sm mt-2">
                <strong>Important:</strong> Always make a manual backup of your system before updating.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const checkUpdatesBtn = document.getElementById('check-updates-btn');
    const updateStatus = document.getElementById('update-status');
    
    if (checkUpdatesBtn) {
      checkUpdatesBtn.addEventListener('click', function() {
        checkUpdatesBtn.disabled = true;
        checkUpdatesBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Checking...';
        
        fetch('{{ route("admin.updates.check") }}')
          .then(response => response.json())
          .then(data => {
            if (data.has_update) {
              // update available
              updateStatus.innerHTML = `
                <div class="alert alert-info">
                  <div class="d-flex align-items-center">
                    <i class="material-symbols-rounded me-2">notifications</i>
                    <div>
                      <h6 class="alert-heading mb-0">Update Available!</h6>
                      <p class="mb-0">Version ${data.latest_version} is now available.</p>
                    </div>
                  </div>
                </div>
                
                <div class="card mb-4">
                  <div class="card-header">
                    <h6 class="mb-0">Version ${data.latest_version}</h6>
                    <p class="text-sm text-muted mb-0">
                      Released: ${new Date(data.published_at).toLocaleDateString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})}
                    </p>
                  </div>
                  <div class="card-body">
                    <h6>Release Notes:</h6>
                    <div class="release-notes">
                      ${data.release_notes.replace(/\n/g, '<br>')}
                    </div>
                  </div>
                  <div class="card-footer">
                    <div class="d-flex justify-content-between">
                      <div>
                        <form action="{{ route('admin.updates.download') }}" method="POST">
                          @csrf
                          <button type="submit" class="btn btn-primary">Download Update</button>
                        </form>
                      </div>
                      
                      ${@json(file_exists(storage_path('app/update.zip'))) ? 
                        `<div>
                          <form action="{{ route('admin.updates.install') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">Install Update</button>
                          </form>
                        </div>` : ``}
                    </div>
                  </div>
                </div>
              `;
            } else {
              // no update
              updateStatus.innerHTML = `
                <div class="alert alert-success">
                  <div class="d-flex align-items-center">
                    <i class="material-symbols-rounded me-2">check_circle</i>
                    <div>
                      <h6 class="alert-heading mb-0">You're up to date!</h6>
                      <p class="mb-0">
                        ${data.message || 'Your system is running the latest version.'}
                      </p>
                    </div>
                  </div>
                </div>
              `;
            }
          })
          .catch(error => {
            updateStatus.innerHTML = `
              <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                  <i class="material-symbols-rounded me-2">error</i>
                  <div>
                    <h6 class="alert-heading mb-0">Error</h6>
                    <p class="mb-0">Failed to check for updates. Please try again later.</p>
                  </div>
                </div>
              </div>
            `;
            console.error('Error checking for updates:', error);
          })
          .finally(() => {
            checkUpdatesBtn.disabled = false;
            checkUpdatesBtn.innerHTML = 'Check for Updates';
          });
      });
    }
  });
</script>
@endsection 
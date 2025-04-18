@extends('layouts.admin', ['hideNavbar' => true])

@section('title', 'Edit Tenant: ' . ($tenant->name ?? 'Tenant'))

@section('content')
<main class="main-content mt-0">
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
                  Edit Barangay {{ $tenant->domain ?? '[name of tenant barangay]' }}
                </h4>
                <p class="text-white text-center mb-2">
                  You can edit the following fields:
                </p>
              </div>
            </div>
            <div class="card-body">
              <div class="row">
                <!-- Left side: Form inputs -->
                <div class="col-md-6">
                  <form role="form" class="text-start" method="POST" action="{{ route('admin.tenants.update', $tenant->id ?? 1) }}">
                    @csrf
                    @method('PUT')
                    <div class="input-group input-group-outline my-3">
                      <label class="form-label">Full Name</label>
                      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tenant->name ?? '') }}" />
                    </div>
                    @error('name')
                    <div class="text-danger text-xs">{{ $message }}</div>
                    @enderror
                    
                    <div class="input-group input-group-outline my-3">
                      <label class="form-label">Email</label>
                      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $tenant->email ?? '') }}" />
                    </div>
                    @error('email')
                    <div class="text-danger text-xs">{{ $message }}</div>
                    @enderror
                    
                    <div class="input-group input-group-outline mb-3">
                      <label class="form-label">Barangay</label>
                      <input type="text" name="domain" class="form-control" value="{{ $tenant->domain ?? '' }}" disabled />
                    </div>
                    <p class="mt-4 text-sm">
                      <b>☹️ You can't Edit Barangay</b><br />
                      This field is linked to your domain, and changing this may cause unwanted issues (such as unreachable domain, 404 Errors, and even loss of data). It's much better if left unchanged. <br><br> <b>🤔 Made a mistake or typo?</b> <br> Scroll down to learn how to remediate this.</p>
                    </p>

                    <div class="text-center">
                      <button
                        type="submit"
                        class="btn bg-gradient-dark w-100 my-4 mb-2"
                      >
                        Update Tenant
                      </button>
                    </div>
                  </form>

                  <form method="POST" action="{{ route('admin.tenants.toggle', $tenant->id ?? 1) }}">
                    @csrf
                    @method('PUT')
                    <div class="text-center">
                      <button
                        type="submit"
                        class="btn bg-gradient-primary w-100 my-4 mb-2"
                      >
                        {{ ($tenant->is_active ?? false) ? 'Deactivate' : 'Activate' }} Tenant
                      </button>
                    </div>
                  </form>
                  <p class="mt-4 text-sm text-center">
                    Although this process is automatic, you can Deactivate a Tenant if they are no longer paying the subscription or they breached the contract.
                  </p>
                  
                  <form method="POST" action="{{ route('admin.tenants.destroy', $tenant->id ?? 1) }}" onsubmit="return confirm('Are you sure you want to remove this tenant? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <p class="mt-4 text-sm text-center">
                      Remove tenant if they made a mistake on their domain name.<br />
                      And let them signup again.
                    </p>
                    <div class="text-center">
                      <button
                        type="submit"
                        class="btn bg-gradient-danger w-100 my-4 mb-2"
                      >
                        REMOVE Tenant
                      </button>
                    </div>
                  </form>
                </div>

                <!-- Right side: Plan picker -->
                <div class="col-md-6">
                  <br />
                  <h5 class="text-center mb-4">Choose a Plan</h5>

                  <form method="POST" action="{{ route('admin.tenants.update-plan', $tenant->id ?? 1) }}">
                    @csrf
                    @method('PUT')
                    
                    <!-- Basic Plan -->
                    <div class="card mb-3">
                      <div class="card-body">
                        <div class="form-check mb-2">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="subscription_plan"
                            id="basic"
                            value="Basic P399"
                            {{ ($tenant->subscription_plan ?? '') == 'Basic P399' ? 'checked' : '' }}
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
                    <div class="card mb-3">
                      <div class="card-body">
                        <div class="form-check mb-2">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="subscription_plan"
                            id="essentials"
                            value="Essentials P799"
                            {{ ($tenant->subscription_plan ?? '') == 'Essentials P799' ? 'checked' : '' }}
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
                    <div class="card mb-3">
                      <div class="card-body">
                        <div class="form-check mb-2">
                          <input
                            class="form-check-input"
                            type="radio"
                            name="subscription_plan"
                            id="ultimate"
                            value="Ultimate P1299"
                            {{ ($tenant->subscription_plan ?? '') == 'Ultimate P1299' ? 'checked' : '' }}
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
                          <li>Page Watermarking</li>
                          <li>Software Updates</li>
                          <li>12 Month Subscription</li>
                        </ul>
                      </div>
                    </div>

                    <div class="text-center">
                      <button
                        type="submit"
                        class="btn bg-gradient-success w-100 my-4 mb-2"
                      >
                        Update Plan
                      </button>
                    </div>
                  </form>
                </div>
              </div>
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

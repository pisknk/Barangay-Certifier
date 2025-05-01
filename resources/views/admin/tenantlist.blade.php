@extends('layouts.admin')

@section('title', 'Tenants')

@section('content')
<div class="container-fluid py-2">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div
          class="card-header p-0 position-relative mt-n4 mx-3 z-index-2"
        >
          <div
            class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3"
          >
            <h6 class="text-white text-capitalize ps-3">Tenants</h6>
          </div>
        </div>
        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th
                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                  >
                    Tenant and Barangay
                  </th>
                  <th
                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                  >
                    Plan and Domain
                  </th>
                  <th
                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                  >
                    Status
                  </th>
                  <th
                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                  >
                    Valid until
                  </th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @forelse($tenants ?? [] as $tenant)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div>
                        <div class="avatar avatar-sm me-3 bg-gradient-primary d-flex align-items-center justify-content-center rounded-circle">
                          <span class="text-white text-xs">{{ substr($tenant->name, 0, 1) }}{{ isset(explode(' ', $tenant->name)[1]) ? substr(explode(' ', $tenant->name)[1], 0, 1) : '' }}</span>
                        </div>
                      </div>
                      <div
                        class="d-flex flex-column justify-content-center"
                      >
                        <h6 class="mb-0 text-sm">{{ $tenant->name }}</h6>
                        <p class="text-xs text-secondary mb-0">
                          {{ $tenant->id }}
                        </p>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">
                      {{ $tenant->subscription_plan }}
                    </p>
                    <p class="text-xs text-secondary mb-0">
                      {{ $tenant->domain_name ?? 'No domain assigned' }}
                    </p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    @php
                    $statusMap = [
                      0 => ['class' => 'bg-secondary', 'text' => 'Not Active'],
                      1 => ['class' => 'bg-success', 'text' => 'Subscribed'],
                      2 => ['class' => 'bg-warning', 'text' => 'Disabled by Admin'],
                      3 => ['class' => 'bg-danger', 'text' => 'Expired Subscription']
                    ];
                    $status = isset($tenant->is_active) ? (int)$tenant->is_active : 0;
                    $statusInfo = $statusMap[$status] ?? $statusMap[0];
                    @endphp
                    <span class="badge badge-sm {{ $statusInfo['class'] }}">{{ $statusInfo['text'] }}</span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">
                      {{ $tenant->valid_until ? date('M d, Y', strtotime($tenant->valid_until)) : '----' }}
                    </span>
                  </td>
                  <td class="align-middle">
                    <a
                      href="{{ route('admin.tenants.edit', $tenant->id) }}"
                      class="text-secondary font-weight-bold text-xs"
                    >
                      Edit
                    </a>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-4">
                    <p class="text-sm mb-0">No tenants found</p>
                  </td>
                </tr>
                @endforelse

                <!-- Example tenants for demonstration -->
                @if(empty($tenants))
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div>
                        <!-- avatar based on the first 2 letters of their name -->
                        <div class="avatar avatar-sm me-3 bg-gradient-primary d-flex align-items-center justify-content-center rounded-circle">
                          <span class="text-white text-xs">JM</span>
                        </div>
                      </div>
                      <div
                        class="d-flex flex-column justify-content-center"
                      >
                        <h6 class="mb-0 text-sm">Jenna Marbles</h6>
                        <p class="text-xs text-secondary mb-0">
                          Casisang
                        </p>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">
                      Basic P399
                    </p>
                    <!-- base this on the "plan" field in the "tenants" table -->
                    <p class="text-xs text-secondary mb-0">
                      casisang.localhost:8000
                    </p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm bg-success"
                      >Subscribed</span
                    >
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold"
                      >May 28, 2025</span
                    >
                    <!-- base this on the "valid_until" field in the "tenants" table -->
                  </td>
                  <td class="align-middle">
                    <a
                      href="{{ route('admin.tenants.edit', 1) }}"
                      class="text-secondary font-weight-bold text-xs"
                    >
                      Edit
                    </a>
                  </td>
                </tr>
                <!-- Add more example entries here -->
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer class="footer py-4">
    <div class="container-fluid">
      <div class="row align-items-center justify-content-lg-between">
        <div class="col-lg-6 mb-lg-0 mb-4">
          <div
            class="copyright text-center text-sm text-muted text-lg-start"
          >
            ©
            <script>
              document.write(new Date().getFullYear());
            </script>
            , made with <i class="fa fa-heart"></i> by
            <a
              href="#"
              class="font-weight-bold"
              target="_blank"
              >Playpass Creative Labs</a
            >
          </div>
        </div>
      </div>
    </div>
  </footer>
</div>
@endsection

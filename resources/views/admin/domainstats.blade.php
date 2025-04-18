@extends('layouts.admin')

@section('title', 'Domain Status')

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
            <h6 class="text-white text-capitalize ps-3">Domain Status</h6>
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
                    Domain
                  </th>
                  <th
                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                  >
                    Plan and Tenant
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
                </tr>
              </thead>
              <tbody>
                @forelse($domains ?? [] as $domain)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div>
                        <!-- avatar based on the first 2 letters of their name -->
                      </div>
                      <div
                        class="d-flex flex-column justify-content-center"
                      >
                        <h6 class="mb-0 text-sm">
                          {{ $domain->domain }}.{{ config('tenancy.central_domains')[0] }}
                        </h6>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">
                      {{ $domain->subscription_plan }}
                    </p>
                    <p class="text-xs text-secondary mb-0">
                      {{ $domain->tenant_name }}
                    </p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    @if($domain->is_active)
                    <span class="badge badge-sm bg-gradient-success">Up and Running</span>
                    @elseif($domain->status == 'disabled')
                    <span class="badge badge-sm bg-gradient-primary">Domain Disabled</span>
                    @elseif($domain->status == 'unreachable')
                    <span class="badge badge-sm bg-gradient-danger">Domain Unreachable</span>
                    @else
                    <span class="badge badge-sm bg-gradient-secondary">Not yet Activated</span>
                    @endif
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold">
                      {{ $domain->valid_until ? date('M d, Y', strtotime($domain->valid_until)) : '----' }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="text-center py-4">
                    <p class="text-sm mb-0">No domains found</p>
                  </td>
                </tr>
                @endforelse

                <!-- Example domains for demonstration -->
                @if(empty($domains))
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div>
                        <!-- avatar based on the first 2 letters of their name -->
                      </div>
                      <div
                        class="d-flex flex-column justify-content-center"
                      >
                        <h6 class="mb-0 text-sm">
                          casisang.localhost:8000
                        </h6>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">
                      Basic P399
                    </p>
                    <p class="text-xs text-secondary mb-0">
                      Jenna Marbles
                    </p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm bg-gradient-success"
                      >Up and Running</span
                    >
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold"
                      >May 28, 2025</span
                    >
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div>
                        <!-- avatar based on the first 2 letters of their name -->
                      </div>
                      <div
                        class="d-flex flex-column justify-content-center"
                      >
                        <h6 class="mb-0 text-sm">
                          san-jose.localhost:8000
                        </h6>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">
                      Essentials P799
                    </p>
                    <p class="text-xs text-secondary mb-0">John Doe</p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm bg-gradient-primary"
                      >Domain Disabled</span
                    >
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold"
                      >January 28, 2025</span
                    >
                  </td>
                </tr>
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div>
                        <!-- avatar based on the first 2 letters of their name -->
                      </div>
                      <div
                        class="d-flex flex-column justify-content-center"
                      >
                        <h6 class="mb-0 text-sm">
                          high-sierra.localhost:8000
                        </h6>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">
                      Ultimate P1299
                    </p>
                    <p class="text-xs text-secondary mb-0">
                      Juan Dela Cruz
                    </p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge badge-sm bg-gradient-secondary"
                      >Not yet Activated</span
                    >
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs font-weight-bold"
                      >----</span
                    >
                  </td>
                </tr>
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

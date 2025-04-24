<aside
  class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2"
  id="sidenav-main"
>
  <div class="sidenav-header">
    <i
      class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
      aria-hidden="true"
      id="iconSidenav"
    ></i>
    <a
      class="navbar-brand px-4 py-3 m-0"
      href="{{ route('tenant.certificates.index') }}"
    >
      <img
        src="https://em-content.zobj.net/source/telegram/386/classical-building_1f3db-fe0f.webp"
        class="navbar-brand-img"
        width="26"
        height="26"
        alt="main_logo"
      />
      <span class="ms-1 text-sm text-dark">Barangay Certifier</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0 mb-2" />
  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">
      @if(Auth::guard('tenant')->user()->isAdmin())
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('tenant.users.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('tenant.users.index') }}">
          <i class="material-symbols-rounded opacity-5">group</i>
          <span class="nav-link-text ms-1">Users</span>
        </a>
      </li>
      @endif
      
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('tenant.certificates.*') || request()->routeIs('tenant.dashboard') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('tenant.certificates.index') }}">
          <i class="material-symbols-rounded opacity-5">receipt_long</i>
          <span class="nav-link-text ms-1">Certificates</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('tenant.settings.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('tenant.settings.index') }}">
          <i class="material-symbols-rounded opacity-5">settings</i>
          <span class="nav-link-text ms-1">Settings</span>
        </a>
      </li>
    </ul>
  </div>
  <div class="sidenav-footer position-absolute w-100 bottom-0">
    <div class="mx-3">
      <form method="POST" action="{{ route('tenant.logout') }}">
        @csrf
        <button type="submit" class="btn bg-gradient-dark w-100">Log out</button>
      </form>
    </div>
  </div>
</aside> 
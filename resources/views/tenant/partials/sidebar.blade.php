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
      href="{{ route('admin.console') }}"
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
      <li class="nav-item">
        <a
          class="nav-link {{ request()->routeIs('admin.console') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
          href="{{ route('admin.console') }}"
        >
          <i class="material-symbols-rounded opacity-5">dashboard</i>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.tenants') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('admin.tenants') }}">
          <i class="material-symbols-rounded opacity-5">table_view</i>
          <span class="nav-link-text ms-1">Tenants</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.domains') ? 'active bg-gradient-dark text-white' : 'text-dark' }}" href="{{ route('admin.domains') }}">
          <i class="material-symbols-rounded opacity-5">receipt_long</i>
          <span class="nav-link-text ms-1">Domain Status</span>
        </a>
      </li>
    </ul>
  </div>
  <div class="sidenav-footer position-absolute w-100 bottom-0">
    <div class="mx-3">
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="btn bg-gradient-dark w-100">Log out</button>
      </form>
    </div>
  </div>
</aside> 
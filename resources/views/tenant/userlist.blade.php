@extends('layouts.tenant')

@section('title', 'User List')

@section('content')
<div class="container-fluid py-2">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">
        <div
          class="card-header p-0 position-relative mt-n4 mx-3 z-index-2"
        >
          <div
            class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center"
          >
            <h6 class="text-white text-capitalize ps-3">User List</h6>
            <a href="{{ route('tenant.users.create') }}" class="btn btn-sm bg-gradient-secondary me-3">
              <i class="material-symbols-rounded">add</i> Add User
            </a>
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
                    User and Role
                  </th>
                  <th
                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                  >
                    Position
                  </th>
                  <th
                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                  >
                    Phone
                  </th>
                  <th class="text-secondary opacity-7"></th>
                </tr>
              </thead>
              <tbody>
                @forelse($users ?? [] as $user)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      <div>
                        <div class="avatar avatar-sm me-3 bg-gradient-primary d-flex align-items-center justify-content-center rounded-circle">
                          <span class="text-white text-xs">{{ substr($user->name, 0, 1) }}{{ isset(explode(' ', $user->name)[1]) ? substr(explode(' ', $user->name)[1], 0, 1) : '' }}</span>
                        </div>
                      </div>
                      <div
                        class="d-flex flex-column justify-content-center"
                      >
                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                        <p class="text-xs text-secondary mb-0">
                          {{ $user->email }}
                        </p>
                        <span class="badge badge-sm {{ $user->role === 'admin' ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                          {{ ucfirst($user->role) }}
                        </span>
                      </div>
                    </div>
                  </td>
                  <td>
                    <p class="text-xs font-weight-bold mb-0">
                      {{ $user->position ?? 'No position' }}
                    </p>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <p class="text-xs font-weight-bold mb-0">
                      {{ $user->phone ?? 'No phone' }}
                    </p>
                  </td>
                  <td class="align-middle">
                    <div class="d-flex">
                      <a
                        href="{{ route('tenant.users.edit', $user->id) }}"
                        class="text-secondary font-weight-bold text-xs me-2"
                      >
                        Edit
                      </a>
                      @if($user->role !== 'admin')
                      <form action="{{ route('tenant.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-danger font-weight-bold text-xs border-0 bg-transparent p-0">
                          Delete
                        </button>
                      </form>
                      @endif
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="text-center py-4">
                    <p class="text-sm mb-0">No users found</p>
                    <a href="{{ route('tenant.users.create') }}" class="btn btn-sm bg-gradient-dark mt-2">Add your first user</a>
                  </td>
                </tr>
                @endforelse
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

@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header bg-success text-white">
      <strong class="me-auto">Success</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      {{ session('success') }}
    </div>
  </div>
</div>
<script>
  // Auto hide the toast after 5 seconds
  setTimeout(() => {
    const toastElement = document.querySelector('.toast');
    const toast = new bootstrap.Toast(toastElement);
    toast.hide();
  }, 5000);
</script>
@endif

@endsection

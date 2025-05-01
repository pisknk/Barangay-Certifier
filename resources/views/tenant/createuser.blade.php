@extends('layouts.tenant')

@section('title', 'Create New User')

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
            <h6 class="text-white text-capitalize ps-3">Create New User</h6>
            <a href="{{ route('tenant.users.index') }}" class="btn btn-sm bg-gradient-secondary me-3">
              <i class="material-symbols-rounded">arrow_back</i> Back to Users
            </a>
          </div>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('tenant.users.store') }}">
            @csrf
            
            <div class="row">
              <div class="col-md-6">
                <div class="input-group input-group-outline my-3 @error('name') is-invalid @enderror">
                  <label class="form-label">Full Name</label>
                  <input type="text" name="name" class="form-control" value="{{ old('name') }}" required />
                </div>
                @error('name')
                <div class="text-danger text-xs">{{ $message }}</div>
                @enderror
                
                <div class="input-group input-group-outline my-3 @error('email') is-invalid @enderror">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" value="{{ old('email') }}" required />
                </div>
                @error('email')
                <div class="text-danger text-xs">{{ $message }}</div>
                @enderror
                
                <div class="input-group input-group-outline my-3 @error('password') is-invalid @enderror">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" required />
                </div>
                @error('password')
                <div class="text-danger text-xs">{{ $message }}</div>
                @enderror
              </div>
              
              <div class="col-md-6">
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">Position</label>
                  <input type="text" name="position" class="form-control" value="{{ old('position') }}" />
                </div>
                
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">Phone</label>
                  <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" />
                </div>
                
                <div class="my-3">
                  <label class="ms-0">Role</label>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="role" id="roleUser" value="user" {{ old('role') == 'user' ? 'checked' : 'checked' }}>
                    <label class="form-check-label" for="roleUser">
                      User
                    </label>
                  </div>
                  <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="role" id="roleAdmin" value="admin" {{ old('role') == 'admin' ? 'checked' : '' }}>
                    <label class="form-check-label" for="roleAdmin">
                      Admin
                    </label>
                  </div>
                  @error('role')
                  <div class="text-danger text-xs">{{ $message }}</div>
                  @enderror
                </div>
              </div>
            </div>
            
            <div class="row mt-3">
              <div class="col-12 text-center">
                <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">
                  Create User
                </button>
              </div>
            </div>
          </form>
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
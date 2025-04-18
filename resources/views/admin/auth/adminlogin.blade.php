@extends('layouts.admin', ['hideNavbar' => true, 'hideConfigPanel' => true])

@section('title', 'Admin Panel Login')

@section('content')
<main class="main-content mt-0">
  <div
    class="page-header align-items-start min-vh-100"
    style="
      background-image: url('https://malaybalaycity.gov.ph/wp-content/uploads/2022/08/CITYHALL-scaled.jpg');
    "
  >
    <span class="mask bg-gradient-dark opacity-6"></span>
    <div class="container my-auto">
      <div class="row">
        <div class="col-lg-4 col-md-8 col-12 mx-auto">
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
                  Login to the Admin Panel
                </h4>
                <br />
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="input-group input-group-outline my-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus />
                </div>
                @error('email')
                <div class="text-danger text-xs">{{ $message }}</div>
                @enderror
                
                <div class="input-group input-group-outline mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required />
                </div>
                @error('password')
                <div class="text-danger text-xs">{{ $message }}</div>
                @enderror
                
                <div
                  class="form-check form-switch d-flex align-items-center mb-3"
                >
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="remember"
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}
                  />
                  <label class="form-check-label mb-0 ms-3" for="remember"
                    >Remember me</label
                  >
                </div>
                <div class="text-center">
                  <button
                    type="submit"
                    class="btn bg-gradient-dark w-100 my-4 mb-2"
                  >
                    Sign in
                  </button>
                </div>
                <p class="mt-4 text-sm text-center">
                  If you are a tenant, please check your email for login
                  instructions.
                </p>
              </form>
            </div>
          </div>
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

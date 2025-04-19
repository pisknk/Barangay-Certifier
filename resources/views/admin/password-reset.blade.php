@extends('layouts.admin', ['hideNavbar' => true])

@section('title', 'Emergency Password Reset')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                        <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Emergency Password Reset</h4>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <p>{{ session('success') }}</p>
                            <p>Email: {{ session('tenant_email') }}</p>
                            <p>You can now <a href="{{ session('tenant_url') }}" class="alert-link">log in to your tenant</a>.</p>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ url('/admin/password-reset') }}">
                        @csrf
                        
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Tenant ID</label>
                            <input type="text" name="tenant_id" class="form-control" required>
                        </div>
                        
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="input-group input-group-outline my-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn bg-gradient-primary w-100 my-4 mb-2">Reset Password</button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="mt-4 text-sm">
                            <a href="{{ url('/admin/console') }}" class="text-primary text-gradient font-weight-bold">Back to Admin Panel</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5>How to fix tenant login issues:</h5>
                    <ol>
                        <li>Enter your tenant ID (found in the tenant list in admin panel)</li>
                        <li>Enter your email address</li>
                        <li>Enter a new password (6+ characters)</li>
                        <li>Click Reset Password</li>
                        <li>Use your email and the new password to log in to your tenant</li>
                    </ol>
                    <p class="text-info">This tool synchronizes passwords between the tenant record and the user account to fix login issues.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
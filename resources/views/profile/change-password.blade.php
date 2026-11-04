<!-- resources/views/profile/change-password.blade.php -->
@extends('layouts.app')

@section('title', 'Change Password')

@section('sidebar')
    @if(Auth::user()->role === 'admin')
        @include('admin.partials.sidebar')
    @elseif(Auth::user()->role === 'teacher')
        @include('teacher.partials.sidebar')
    @else
        @include('student.partials.sidebar')
    @endif
@endsection

@section('page-title', 'Change Password')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-shield-lock text-primary"></i> Update Your Password</h6>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" 
                               class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" 
                               class="form-control @error('password') is-invalid @enderror" required>
                        <small class="text-muted">Minimum 8 characters.</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" 
                               class="form-control" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
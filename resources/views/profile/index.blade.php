@extends('layouts.app')
@section('title', 'My Profile')
@section('sidebar')
    @if(Auth::user()->role === 'admin') @include('admin.partials.sidebar')
    @elseif(Auth::user()->role === 'teacher') @include('teacher.partials.sidebar')
    @else @include('student.partials.sidebar') @endif
@endsection
@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header bg-white"><h6 class="mb-0">My Profile & Signature</h6></div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Upload Signature</label>
                        @if(Auth::user()->signature_path)
                            <div class="mb-2">
                                <img src="{{ url(Auth::user()->signature_path) }}" style="max-height: 80px;" class="border p-1 rounded">
                            </div>
                        @endif
                        <input type="file" name="signature" class="form-control" accept="image/*">
                        <small class="text-muted">Upload a transparent PNG for best results.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Signature</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
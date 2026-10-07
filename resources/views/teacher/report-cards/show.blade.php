@extends('layouts.app')

@section('title', 'Report Card')

@section('sidebar')
    @include('teacher.partials.sidebar')
@endsection

@section('page-title', 'Report Card')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card report-card-wrapper">
            <div class="card-header bg-white no-print">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Report Card Preview</h5>
                    <div>
                        <button onclick="window.print()" class="btn btn-primary btn-sm">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <a href="{{ route('teacher.report-cards.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @include('partials.report-card-body', ['reportCard' => $reportCard])
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @include('partials.report-card-styles')
@endpush
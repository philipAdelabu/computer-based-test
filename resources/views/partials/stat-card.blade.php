{{-- Usage:
    @include('partials.stat-card', [
        'color' => 'primary',
        'icon' => 'bi-people',
        'label' => 'Students',
        'value' => $totalStudents,
    ])
--}}
<div class="col-6 col-md-4 col-lg-3 col-xl-3">
    <div class="stat-card" style="border-left-color: var(--bs-{{ $color }});">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-muted mb-1">{{ $label }}</h6>
                <h3 class="mb-0">{{ $value }}</h3>
            </div>
            <div class="stat-icon text-{{ $color }}">
                <i class="bi {{ $icon }}"></i>
            </div>
        </div>
    </div>
</div>
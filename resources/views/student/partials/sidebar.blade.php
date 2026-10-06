<!-- resources/views/student/partials/sidebar.blade.php -->
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" 
           href="{{ route('student.dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.exams*') ? 'active' : '' }}" 
           href="{{ route('student.exams') }}">
            <i class="bi bi-file-text-fill"></i> Tests & Exams
            @if(isset($availableExamsCount) && $availableExamsCount > 0)
                <span class="badge bg-danger rounded-pill ms-1">{{ $availableExamsCount }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.results') ? 'active' : '' }}" 
           href="{{ route('student.results') }}">
            <i class="bi bi-bar-chart-fill"></i> Results
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('student.report-cards*') ? 'active' : '' }}" 
           href="{{ route('student.report-cards') }}">
            <i class="bi bi-file-earmark-text-fill"></i> Report Cards
        </a>
    </li>
</ul>
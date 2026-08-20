<!-- resources/views/teacher/partials/sidebar.blade.php -->
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" 
           href="{{ route('teacher.dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('teacher.subjects') ? 'active' : '' }}" 
           href="{{ route('teacher.subjects') }}">
            <i class="bi bi-book-fill"></i> My Subjects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#uploadScoreModal">
            <i class="bi bi-upload"></i> Upload Scores
        </a>
    </li>
</ul>
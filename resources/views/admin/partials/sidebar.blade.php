<!-- resources/views/admin/partials/sidebar.blade.php -->
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
           href="{{ route('admin.dashboard') }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}" 
           href="{{ route('admin.students') }}">
            <i class="bi bi-people-fill"></i> Students
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}" 
           href="{{ route('admin.teachers') }}">
            <i class="bi bi-person-badge-fill"></i> Teachers
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.classes*') ? 'active' : '' }}" 
           href="{{ route('admin.classes') }}">
            <i class="bi bi-building-fill"></i> Classes
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.subjects*') ? 'active' : '' }}" 
           href="{{ route('admin.subjects') }}">
            <i class="bi bi-book-fill"></i> Subjects
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.questions*') ? 'active' : '' }}" 
           href="{{ route('admin.questions') }}">
            <i class="bi bi-question-circle-fill"></i> Questions
        </a>
    </li>
</ul>
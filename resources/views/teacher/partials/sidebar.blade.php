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
        <a class="nav-link {{ request()->routeIs('teacher.questions*') ? 'active' : '' }}" 
           href="{{ route('teacher.questions') }}">
            <i class="bi bi-question-circle-fill"></i> Question Bank
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('teacher.exams*') ? 'active' : '' }}" 
           href="{{ route('teacher.exams') }}">
            <i class="bi bi-file-text-fill"></i> Tests & Exams
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('teacher.report-cards*') ? 'active' : '' }}" 
           href="{{ route('teacher.report-cards.index') }}">
            <i class="bi bi-file-earmark-text-fill"></i> Report Cards
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('teacher.scores*') ? 'active' : '' }}" 
        href="{{ route('teacher.scores') }}">
            <i class="bi bi-clipboard-check-fill"></i> Upload Scores
        </a>
   </li>
</ul>
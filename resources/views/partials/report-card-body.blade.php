{{--
    Comprehensive Report Card Body
    Includes:
    - School header with logo, name, contact
    - Student info table with photo
    - Result analysis
    - Cognitive ability table (all class subjects, dashes for unattempted)
    - Affective traits & psychomotor skills
    - Attendance
    - Comments & signatures
    - Vacation/resumption dates
--}}

@php
    $logoUrl = \App\Helpers\SettingHelper::schoolLogo();
    $schoolName = \App\Helpers\SettingHelper::schoolName();
    $schoolEmail = \App\Helpers\SettingHelper::schoolEmail();
    $schoolPhone = \App\Helpers\SettingHelper::schoolPhone();
    $schoolAddress = \App\Helpers\SettingHelper::schoolAddress();
    $schoolMotto = \App\Helpers\SettingHelper::schoolMotto();

    $student = $reportCard->student;
    $user = $student->user;

    // Fallback trait defaults (used if not in DB)
    $defaultAffective = [
        'Punctuality' => 4,
        'Mental Alertness' => 4,
        'Respect' => 4,
        'Neatness' => 4,
        'Politeness' => 4,
        'Honesty' => 4,
        'Peer Relationship' => 4,
        'Willingness To Learn' => 4,
        'Teamwork' => 4,
    ];

    $defaultPsychomotor = [
        'Verbal Skills' => 4,
        'Sports & Games' => 4,
        'Artistic Skills' => 4,
        'Music' => 3,
        'Dance' => 3,
        'Entrepreneurship' => 4,
        'Chess' => 3,
    ];

    $affective = !empty($reportCard->affective_traits) 
        ? $reportCard->affective_traits 
        : $defaultAffective;

    $psychomotor = !empty($reportCard->psychomotor_skills) 
        ? $reportCard->psychomotor_skills 
        : $defaultPsychomotor;

    // Session year display
    $sessionDisplay = $reportCard->session 
        ?? ($reportCard->academic_year . '/' . ($reportCard->academic_year + 1));

    // Age: use DB field or compute from DOB
    $ageDisplay = $reportCard->age 
        ?? ($student->date_of_birth ? $student->date_of_birth->age : null);

    // Class teacher name fallback
    $classTeacherName = $reportCard->class_teacher_name 
        ?? ($reportCard->class->classTeacher->name ?? null);
@endphp

<div class="report-card-content">

    <!-- ================================================ -->
    <!-- HEADER BANNER                                    -->
    <!-- ================================================ -->
    <div class="rc-header-banner">
        <div class="rc-header-logo">
            @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="Logo">
            @else
                <div class="rc-logo-placeholder">
                    <div>SCHOOL</div>
                    <div>LOGO</div>
                </div>
            @endif
        </div>

        <div class="rc-header-text">
            <div class="rc-school-name">{{ strtoupper($schoolName) }}</div>
            @if($schoolAddress)
                <div class="rc-school-contact">{{ $schoolAddress }}</div>
            @endif
            <div class="rc-school-contact-row">
                @if($schoolEmail)
                    <span>Email: {{ $schoolEmail }}</span>
                @endif
                @if($schoolEmail && $schoolPhone)
                    <span class="rc-sep">|</span>
                @endif
                @if($schoolPhone)
                    <span>Tel: {{ $schoolPhone }}</span>
                @endif
            </div>
            @if($schoolMotto)
                <div class="rc-school-motto">"{{ $schoolMotto }}"</div>
            @endif
        </div>

        <div class="rc-header-photo">
            @if($user->has_photo)
                <img src="{{ $user->photo_url }}" alt="{{ $user->name }}">
            @else
                <div class="rc-photo-placeholder">
                    <i class="bi bi-person-fill"></i>
                </div>
            @endif
        </div>
    </div>

    <!-- ================================================ -->
    <!-- STUDENT INFO TABLE                               -->
    <!-- ================================================ -->
    <table class="rc-info-table">
        <tr>
            <td class="rc-info-label">NAME:</td>
            <td class="rc-info-value">{{ strtoupper($user->name) }}</td>
            <td class="rc-info-label">SEX/GENDER:</td>
            <td class="rc-info-value"> {{ strtoupper($reportCard->gender ?? $student->gender ?? 'N/A') }}</td>
            <td class="rc-info-label">AGE:</td>
            <td class="rc-info-value">{{ $ageDisplay ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="rc-info-label">STUDENT CLASS:</td>
            <td class="rc-info-value">{{ strtoupper($reportCard->class->name ?? 'N/A') }}</td>
            <td class="rc-info-label">HOUSE:</td>
            <td class="rc-info-value">{{ strtoupper($reportCard->house ?? 'N/A') }}</td>
            <td class="rc-info-label">ADMISSION NUMBER:</td>
            <td class="rc-info-value">{{ $student->admission_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="rc-info-label">NO. OF STUDENTS IN CLASS:</td>
            <td class="rc-info-value">{{ $reportCard->no_in_class ?? $reportCard->total_students ?? 'N/A' }}</td>
            <td class="rc-info-label">TERM:</td>
            <td class="rc-info-value">{{ strtoupper($reportCard->term) }}</td>
            <td class="rc-info-label">YEAR/SESSION:</td>
            <td class="rc-info-value">{{ $sessionDisplay }}</td>
        </tr>
    </table>

    <!-- ================================================ -->
    <!-- RESULT ANALYSIS                                  -->
    <!-- ================================================ -->
    <div class="rc-section-title">RESULT ANALYSIS</div>
    <div class="rc-analysis-row">
        <div class="rc-analysis-box">
            <div class="rc-analysis-label">Maximum Class Score:</div>
            <div class="rc-analysis-value">
                {{ $reportCard->max_class_score !== null ? number_format($reportCard->max_class_score, 2) : '—' }}
            </div>
        </div>
        <div class="rc-analysis-box">
            <div class="rc-analysis-label">Minimum Class Score:</div>
            <div class="rc-analysis-value">
                {{ $reportCard->min_class_score !== null ? number_format($reportCard->min_class_score, 2) : '—' }}
            </div>
        </div>
        <div class="rc-analysis-box">
            <div class="rc-analysis-label">Marks Obtainable:</div>
            <div class="rc-analysis-value">
                {{ $reportCard->marks_obtainable ?? $reportCard->grand_max ?? '—' }}
            </div>
        </div>
        <div class="rc-analysis-box">
            <div class="rc-analysis-label">Total Score:</div>
            <div class="rc-analysis-value">{{ number_format($reportCard->grand_total, 2) }}</div>
        </div>
        <div class="rc-analysis-box">
            <div class="rc-analysis-label">Overall Percentage:</div>
            <div class="rc-analysis-value">{{ number_format($reportCard->overall_percentage, 2) }}%</div>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- COGNITIVE ABILITY (SUBJECT SCORES)               -->
    <!-- ================================================ -->
    <div class="rc-section-title">COGNITIVE ABILITY</div>
    <table class="rc-scores-table">
        <thead>
            <tr>
                <th rowspan="2" class="rc-th-subject">SUBJECT</th>
                <th colspan="2" class="rc-th-ca">CA</th>
                <th colspan="2" class="rc-th-exam">EXAM</th>
                <th rowspan="2" class="rc-th-small">TOTAL<br>100</th>
                <th rowspan="2" class="rc-th-small">2<sup>nd</sup><br>TERM</th>
                <th rowspan="2" class="rc-th-small">1<sup>st</sup><br>TERM</th>
                <th rowspan="2" class="rc-th-small">SESSION<br>AVG.</th>
                <th rowspan="2" class="rc-th-grade">GRADE</th>
                <th rowspan="2" class="rc-th-remark">REMARK</th>
            </tr>
            <tr>
                <th class="rc-th-small">SCORE</th>
                <th class="rc-th-small">40</th>
                <th class="rc-th-small">SCORE</th>
                <th class="rc-th-small">60</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportCard->subject_scores as $score)
                @php
                    $attempted = $score['attempted'] ?? false;
                    $testAttempted = $score['test_attempted'] ?? false;
                    $examAttempted = $score['exam_attempted'] ?? false;

                    // Convert to a 40/60 scale
                    $caScore40 = null;
                    $examScore60 = null;
                    $total100 = null;

                    if ($attempted) {
                        $testPct = ($score['test_max'] ?? 0) > 0 && $score['test_score'] !== null
                            ? (($score['test_score'] ?? 0) / $score['test_max']) * 100
                            : 0;
                        $examPct = ($score['exam_max'] ?? 0) > 0 && $score['exam_score'] !== null
                            ? (($score['exam_score'] ?? 0) / $score['exam_max']) * 100
                            : 0;

                        $caScore40 = round(($testPct / 100) * 40, 1);
                        $examScore60 = round(($examPct / 100) * 60, 1);
                        $total100 = round($caScore40 + $examScore60, 1);
                    }

                    // Term scores (if provided by service)
                    $termScores = $score['term_scores'] ?? [];
                    $firstTerm = $termScores['first'] ?? null;
                    $secondTerm = $termScores['second'] ?? null;
                    $sessionAvg = $score['session_avg'] ?? $total100;
                @endphp
                <tr class="{{ !$attempted ? 'rc-row-unattempted' : '' }}">
                    <td class="rc-td-subject">{{ strtoupper($score['subject'] ?? 'N/A') }}</td>

                    @if($attempted)
                        {{-- Test Score + Max --}}
                        <td class="rc-td-num">{{ $caScore40 }}</td>
                        <td class="rc-td-num">{{ $caScore40 }}</td>

                        {{-- Exam Score + Max --}}
                        <td class="rc-td-num">{{ $examScore60 }}</td>
                        <td class="rc-td-num">{{ $total100 }}</td>

                        {{-- Total 100 --}}
                        <td class="rc-td-num"><strong>{{ $total100 }}</strong></td>

                        {{-- Previous terms --}}
                        <td class="rc-td-num">
                            {{ $secondTerm !== null ? number_format($secondTerm, 1) : '—' }}
                        </td>
                        <td class="rc-td-num">
                            {{ $firstTerm !== null ? number_format($firstTerm, 1) : '—' }}
                        </td>

                        {{-- Session Average --}}
                        <td class="rc-td-num">
                            {{ $sessionAvg !== null ? number_format($sessionAvg, 1) : '—' }}
                        </td>

                        {{-- Grade --}}
                        <td class="rc-td-grade">{{ $score['grade'] ?? '—' }}</td>

                        {{-- Remark --}}
                        <td class="rc-td-remark">
                            {{ strtoupper($score['remark'] ?? '—') }}
                        </td>
                    @else
                        {{-- Unattempted — dashes across the board --}}
                        <td class="rc-td-num rc-td-dash">—</td>
                        <td class="rc-td-num rc-td-dash">—</td>
                        <td class="rc-td-num rc-td-dash">—</td>
                        <td class="rc-td-num rc-td-dash">—</td>
                        <td class="rc-td-num rc-td-dash">—</td>
                        <td class="rc-td-num rc-td-dash">
                            {{ $secondTerm !== null ? number_format($secondTerm, 1) : '—' }}
                        </td>
                        <td class="rc-td-num rc-td-dash">
                            {{ $firstTerm !== null ? number_format($firstTerm, 1) : '—' }}
                        </td>
                        <td class="rc-td-num rc-td-dash">
                            {{ $sessionAvg !== null ? number_format($sessionAvg, 1) : '—' }}
                        </td>
                        <td class="rc-td-grade rc-td-dash">—</td>
                        <td class="rc-td-remark rc-td-dash">NOT ATTEMPTED</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">
                        <em>No subjects have been set up for this class yet.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ================================================ -->
    <!-- AFFECTIVE TRAITS & PSYCHOMOTOR SKILLS            -->
    <!-- ================================================ -->
    <div class="rc-traits-wrapper">
        <!-- AFFECTIVE TRAITS -->
        <div class="rc-traits-col">
            <div class="rc-traits-header rc-traits-header-affective">AFFECTIVE TRAITS</div>
            <table class="rc-traits-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>5</th>
                        <th>4</th>
                        <th>3</th>
                        <th>2</th>
                        <th>1</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($affective as $trait => $rating)
                        <tr>
                            <td class="rc-trait-name">{{ $trait }}</td>
                            @for($i = 5; $i >= 1; $i--)
                                <td class="rc-trait-cell">
                                    @if((int) $rating === $i)
                                        <span class="rc-trait-check">✓</span>
                                    @else
                                        <span class="rc-trait-box"></span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- PSYCHOMOTOR SKILLS -->
        <div class="rc-traits-col">
            <div class="rc-traits-header rc-traits-header-psychomotor">PSYCHOMOTOR SKILLS</div>
            <table class="rc-traits-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>5</th>
                        <th>4</th>
                        <th>3</th>
                        <th>2</th>
                        <th>1</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($psychomotor as $skill => $rating)
                        <tr>
                            <td class="rc-trait-name">{{ $skill }}</td>
                            @for($i = 5; $i >= 1; $i--)
                                <td class="rc-trait-cell">
                                    @if((int) $rating === $i)
                                        <span class="rc-trait-check">✓</span>
                                    @else
                                        <span class="rc-trait-box"></span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- ATTENDANCE ANALYSIS                              -->
    <!-- ================================================ -->
    <div class="rc-attendance-wrapper">
        <table class="rc-attendance-table">
            <tr>
                <td class="rc-attendance-label" colspan="2">Attendance Analysis:</td>
            </tr>
            <tr>
                <td>Times School Opened:</td>
                <td class="rc-attendance-value">{{ $reportCard->times_school_opened ?? '—' }}</td>
            </tr>
            <tr>
                <td>Times Present:</td>
                <td class="rc-attendance-value">{{ $reportCard->times_present ?? '—' }}</td>
            </tr>
        </table>
    </div>

    <!-- ================================================ -->
    <!-- OVERALL COMMENTS / REMARKS                       -->
    <!-- ================================================ -->
    <div class="rc-section-title">OVERALL COMMENTS / REMARKS</div>
    <table class="rc-comments-table">
        <!-- Class Teacher's Remark -->
        <tr>
            <td class="rc-comments-label">Class Teacher's Remark</td>
            <td class="rc-comments-cell">
                <div class="rc-comment-row">
                    <strong>Name:</strong> 
                    {{ $classTeacherName ?? '—' }}
                </div>
                <div class="rc-comment-row">
                    <strong>Comment:</strong> 
                    {{ $reportCard->class_teacher_comment ?? '—' }}
                </div>
            </td>
            <td class="rc-comments-sign">
                <div class="rc-signature-label">Signature:</div>
                <div class="rc-signature-space"></div>
            </td>
        </tr>

        <!-- Principal's Remark -->
        <tr>
            <td class="rc-comments-label">Principal's Remark</td>
            <td class="rc-comments-cell">
                <div class="rc-comment-row">
                    <strong>Name:</strong> 
                    {{ $reportCard->principal_name ?? '—' }}
                </div>
                <div class="rc-comment-row">
                    <strong>Comment:</strong> 
                    {{ $reportCard->principal_comment ?? '—' }}
                </div>
            </td>
            <td class="rc-comments-sign">
                <div class="rc-signature-label">Signature:</div>
                <div class="rc-signature-space"></div>
            </td>
        </tr>

        <!-- Vacation & Resumption -->
        <tr>
            <td class="rc-comments-label">VACATION DATE:</td>
            <td class="rc-comments-cell">
                <strong>
                    {{ $reportCard->vacation_date ? strtoupper($reportCard->vacation_date->format('jS F, Y')) : '—' }}
                </strong>
            </td>
            <td class="rc-comments-sign rc-comments-sign-label">
                <div class="rc-signature-label">RESUMPTION DATE:</div>
            </td>
        </tr>
        <tr>
            <td class="rc-comments-label"></td>
            <td class="rc-comments-cell" colspan="2">
                <strong>
                    {{ $reportCard->resumption_date ? strtoupper($reportCard->resumption_date->format('jS F, Y')) : '—' }}
                </strong>
            </td>
        </tr>
    </table>

</div>
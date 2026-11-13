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
     <!--
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

-->


  <!-- ================================================ -->
    <!-- COGNITIVE ABILITY (SUBJECT SCORES)               -->
    <!-- ================================================ -->
    <div class="rc-section-title">COGNITIVE ABILITY</div>
    @php
        // Get Admin Settings for CA and Exam weights
        $caWeight = (float) \App\Models\Setting::get('ca_weight', 40);
        $examWeight = (float) \App\Models\Setting::get('exam_weight', 60);
    @endphp

    <style>
        .thead-line th {
      border: 1px solid #dee2e6;/* solid black line */
}
</style>

    <div class="table-responsive">
        <table class="rc-scores-table">
            <thead class="thead-line">
                <!-- TOP ROW: GROUPINGS -->
                <tr class="rc-th-group-row mt-3"">
                    <th rowspan="2" class="rc-th-subject" style="vertical-align: middle;">SUBJECT</th>
                    
                    <!-- 1st Term Group -->
                    <th colspan="3" class="rc-th-term-group" style="padding-top: 10px; border:">1<sup>ST</sup> TERM</th>
                    
                    <!-- 2nd Term Group -->
                    <th colspan="3" class="rc-th-term-group" style="padding-top: 10px;">2<sup>ND</sup> TERM</th>
                    
                    <!-- 3rd Term Group -->
                    <th colspan="3" class="rc-th-term-group" style="padding-top: 10px;">3<sup>RD</sup> TERM</th>
                    
                    <!-- Session Summary -->
                    <th rowspan="2" class="rc-th-session" style="vertical-align: middle;">SESSION<br>AVG</th>
                    <th rowspan="2" class="rc-th-grade" style="vertical-align: middle;">GRADE</th>
                    <th rowspan="2" class="rc-th-remark" style="vertical-align: middle;">REMARK</th>
                </tr>
                
                <!-- SECOND ROW: SUB-COLUMNS -->
                <tr>
                    <!-- 1st Term Sub-columns -->
                    <th class="rc-th-sub">CA<br><small>({{ $caWeight }})</small></th>
                    <th class="rc-th-sub">EXAM<br><small>({{ $examWeight }})</small></th>
                    <th class="rc-th-sub">TOTAL</th>
                    
                    <!-- 2nd Term Sub-columns -->
                    <th class="rc-th-sub">CA<br><small>({{ $caWeight }})</small></th>
                    <th class="rc-th-sub">EXAM<br><small>({{ $examWeight }})</small></th>
                    <th class="rc-th-sub">TOTAL</th>
                    
                    <!-- 3rd Term Sub-columns -->
                    <th class="rc-th-sub">CA<br><small>({{ $caWeight }})</small></th>
                    <th class="rc-th-sub">EXAM<br><small>({{ $examWeight }})</small></th>
                    <th class="rc-th-sub">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reportCard->subject_scores as $score)
                    @php
                        $attempted = $score['attempted'] ?? false;
                        
                        // Extract the per-term historical data
                        $termScores = $score['term_scores'] ?? [];
                        
                        $firstTerm  = $termScores['first'] ?? null;
                        $secondTerm = $termScores['second'] ?? null;
                        $thirdTerm  = $termScores['third'] ?? null;

                        // For the CURRENT term, we calculate the live CA/Exam breakdown
                        // We use the service's test/exam data for the current term.
                        $currentCa = null;
                        $currentExam = null;
                        $currentTotal = null;

                        if ($attempted) {
                            $testScore = $score['test_score'] ?? 0;
                            $testMax = $score['test_max'] ?? 1;
                            $examScore = $score['exam_score'] ?? 0;
                            $examMax = $score['exam_max'] ?? 1;

                            $currentCa = round(($testScore / $testMax) * $caWeight, 1);
                            $currentExam = round(($examScore / $examMax) * $examWeight, 1);
                            $currentTotal = round($currentCa + $currentExam, 1);
                        }

                        // Determine which slot holds the CURRENT term's data
                        $currentTermName = $reportCard->term;
                        
                        // Map the live data to the correct display slot
                        $ca1 = ($currentTermName === 'First Term') ? $currentCa : null;
                        $exam1 = ($currentTermName === 'First Term') ? $currentExam : null;
                        $total1 = ($currentTermName === 'First Term') ? $currentTotal : null;

                        $ca2 = ($currentTermName === 'Second Term') ? $currentCa : null;
                        $exam2 = ($currentTermName === 'Second Term') ? $currentExam : null;
                        $total2 = ($currentTermName === 'Second Term') ? $currentTotal : null;

                        $ca3 = ($currentTermName === 'Third Term') ? $currentCa : null;
                        $exam3 = ($currentTermName === 'Third Term') ? $currentExam : null;
                        $total3 = ($currentTermName === 'Third Term') ? $currentTotal : null;

                        $sessionAvg = $score['session_avg'] ?? null;
                    @endphp
                    <tr class="{{ !$attempted ? 'rc-row-unattempted' : '' }}">
                        <td class="rc-td-subject">{{ strtoupper($score['subject'] ?? 'N/A') }}</td>

                        <!-- 1st Term Cells -->
                        <td class="rc-td-num">{{ $ca1 !== null ? number_format($ca1, 1) : '—' }}</td>
                        <td class="rc-td-num">{{ $exam1 !== null ? number_format($exam1, 1) : '—' }}</td>
                        <td class="rc-td-num"><strong>{{ $total1 !== null ? number_format($total1, 1) : '—' }}</strong></td>

                        <!-- 2nd Term Cells -->
                        <td class="rc-td-num">{{ $ca2 !== null ? number_format($ca2, 1) : '—' }}</td>
                        <td class="rc-td-num">{{ $exam2 !== null ? number_format($exam2, 1) : '—' }}</td>
                        <td class="rc-td-num"><strong>{{ $total2 !== null ? number_format($total2, 1) : '—' }}</strong></td>

                        <!-- 3rd Term Cells -->
                        <td class="rc-td-num">{{ $ca3 !== null ? number_format($ca3, 1) : '—' }}</td>
                        <td class="rc-td-num">{{ $exam3 !== null ? number_format($exam3, 1) : '—' }}</td>
                        <td class="rc-td-num"><strong>{{ $total3 !== null ? number_format($total3, 1) : '—' }}</strong></td>

                        <!-- Session Summary -->
                        <td class="rc-td-num">{{ $sessionAvg !== null ? number_format($sessionAvg, 1) : '—' }}</td>
                        <td class="rc-td-grade">{{ $score['grade'] ?? '—' }}</td>
                        <td class="rc-td-remark">{{ strtoupper($score['remark'] ?? '—') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="13" class="text-center text-muted py-4">
                            <em>No subjects have been set up for this class yet.</em>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
                @if($reportCard->class->classTeacher && $reportCard->class->classTeacher->signature_url)
                    <img src="{{ $reportCard->class->classTeacher->signature_url }}" 
                        alt="Teacher Signature" 
                        style="max-height: 35px; max-width: 100px; object-fit: contain; display: block; margin: 0 auto;">
                @else
                    <div class="rc-signature-space"></div>
                @endif
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
                @php $principalSig = \App\Models\Setting::getImageUrl('principal_signature'); @endphp
                @if($principalSig)
                    <img src="{{ $principalSig }}" 
                        alt="Principal Signature" 
                        style="max-height: 35px; max-width: 100px; object-fit: contain; display: block; margin: 0 auto;">
                @else
                    <div class="rc-signature-space"></div>
                @endif
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
              <td  class="rc-comments-sign mb-2" rowspan="2" >
                @php $schoolStamp = \App\Models\Setting::getImageUrl('school_stamp'); @endphp
                @if($schoolStamp)
                    <div style="position: absolute; bottom: 40px; right: 20px; opacity: 0.85;">
                        <img src="{{ $schoolStamp }}" alt="School Stamp" style="max-height: 80px; max-width: 80px; transform: rotate(-15deg);">
                    </div>
                @endif
             </td>
        </tr>
        <tr>
            <td class="rc-comments-label">RESUMPTION DATE:</td>
            <td class="rc-comments-cell">
                <strong>
                    {{ $reportCard->resumption_date ? strtoupper($reportCard->resumption_date->format('jS F, Y')) : '—' }}
                </strong>
            </td>
             
        </tr>
    </table>

</div>
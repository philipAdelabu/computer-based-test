<!-- resources/views/partials/report-card-body.blade.php -->
<div class="report-card-content">
    
    <!-- HEADER -->
    <div class="report-header">
        @php $logoUrl = \App\Helpers\SettingHelper::schoolLogo(); @endphp
        @if($logoUrl)
            <div style="margin-bottom: 4px;">
                <img src="{{ $logoUrl }}" alt="Logo" style="max-height: 40px; max-width: 40px; object-fit: contain;">
            </div>
        @endif
        <div class="report-school-name">{{ \App\Helpers\SettingHelper::schoolName() }}</div>
        @if(\App\Helpers\SettingHelper::schoolMotto())
            <div style="font-size: 9px; color: #666; font-style: italic; margin-top: 1px;">
                {{ \App\Helpers\SettingHelper::schoolMotto() }}
            </div>
        @endif
        <div class="report-title">STUDENT REPORT CARD</div>
        <div class="report-term">
            {{ $reportCard->term }} — {{ $reportCard->academic_year }} Academic Session
        </div>
    </div>
    
    <!-- STUDENT INFO -->
    <div class="report-info">
        <table class="info-table">
            <tr>
                <td class="info-label">Name:</td>
                <td class="info-value"><strong>{{ $reportCard->student->user->name }}</strong></td>
                <td class="info-label">Admission No:</td>
                <td class="info-value">{{ $reportCard->student->admission_number }}</td>
            </tr>
            <tr>
                <td class="info-label">Class:</td>
                <td class="info-value">{{ $reportCard->class->name }}</td>
                <td class="info-label">Position:</td>
                <td class="info-value">
                    <strong>#{{ $reportCard->position ?? 'N/A' }}</strong>
                    of {{ $reportCard->total_students ?? 'N/A' }}
                </td>
            </tr>
        </table>
    </div>
    
    <!-- SUBJECT SCORES TABLE -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 30%;">Subject</th>
                <th style="width: 11%;">Test</th>
                <th style="width: 11%;">Exam</th>
                <th style="width: 11%;">Total</th>
                <th style="width: 9%;">%</th>
                <th style="width: 9%;">Grade</th>
                <th style="width: 15%;">Remark</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @foreach($reportCard->subject_scores as $score)
                <tr>
                    <td class="text-center">{{ $counter++ }}</td>
                    <td><strong>{{ $score['subject'] ?? 'N/A' }}</strong></td>
                    <td class="text-center">
                        {{ $score['test_score'] ?? 0 }}
                        <span class="text-muted">/{{ $score['test_max'] ?? 0 }}</span>
                    </td>
                    <td class="text-center">
                        {{ $score['exam_score'] ?? 0 }}
                        <span class="text-muted">/{{ $score['exam_max'] ?? 0 }}</span>
                    </td>
                    <td class="text-center">
                        <strong>{{ $score['total'] ?? 0 }}</strong>
                        <span class="text-muted">/{{ $score['max'] ?? 0 }}</span>
                    </td>
                    <td class="text-center">{{ $score['percentage'] ?? 0 }}%</td>
                    <td class="text-center">
                        <span class="grade grade-{{ strtolower($score['grade'] ?? 'f') }}">
                            {{ $score['grade'] ?? 'N/A' }}
                        </span>
                    </td>
                    <td>{{ $score['remark'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="report-total">
                <td colspan="2" class="text-end"><strong>TOTAL</strong></td>
                <td class="text-center">
                    <strong>{{ $reportCard->total_test_score }}</strong>
                    <span class="text-muted">/{{ $reportCard->total_test_max }}</span>
                </td>
                <td class="text-center">
                    <strong>{{ $reportCard->total_exam_score }}</strong>
                    <span class="text-muted">/{{ $reportCard->total_exam_max }}</span>
                </td>
                <td class="text-center">
                    <strong>{{ $reportCard->grand_total }}</strong>
                    <span class="text-muted">/{{ $reportCard->grand_max }}</span>
                </td>
                <td class="text-center"><strong>{{ $reportCard->overall_percentage }}%</strong></td>
                <td class="text-center">
                    <span class="grade grade-{{ strtolower($reportCard->grade) }}">
                        {{ $reportCard->grade }}
                    </span>
                </td>
                <td><strong>{{ $reportCard->remarks }}</strong></td>
            </tr>
        </tfoot>
    </table>
    
    <!-- SUMMARY ROW -->
    <div class="report-summary">
        <div class="summary-box">
            <div class="summary-label">Total Score</div>
            <div class="summary-value">{{ $reportCard->grand_total }}/{{ $reportCard->grand_max }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Percentage</div>
            <div class="summary-value">{{ $reportCard->overall_percentage }}%</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Grade</div>
            <div class="summary-value">{{ $reportCard->grade }}</div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Position</div>
            <div class="summary-value">
                #{{ $reportCard->position ?? 'N/A' }}/{{ $reportCard->total_students ?? 'N/A' }}
            </div>
        </div>
        <div class="summary-box">
            <div class="summary-label">Remarks</div>
            <div class="summary-value summary-remark">{{ $reportCard->remarks }}</div>
        </div>
    </div>
    
    <!-- GRADING KEY -->
    <div class="report-grading">
        <span class="grading-title">Grading:</span>
        <span class="grading-item"><strong>A</strong> 80-100 (Excellent)</span>
        <span class="grading-item"><strong>B</strong> 70-79 (Very Good)</span>
        <span class="grading-item"><strong>C</strong> 60-69 (Good)</span>
        <span class="grading-item"><strong>D</strong> 50-59 (Fair)</span>
        <span class="grading-item"><strong>E</strong> 40-49 (Poor)</span>
        <span class="grading-item"><strong>F</strong> Below 40 (Very Poor)</span>
    </div>
    
    <!-- FOOTER -->
    <div class="report-footer">
        <div class="footer-left">
            <div class="signature-line">
                <div class="signature-label">Class Teacher's Signature</div>
                <div class="signature-space"></div>
            </div>
        </div>
        <div class="footer-right">
            <div class="signature-line">
                <div class="signature-label">Principal's Signature</div>
                <div class="signature-space"></div>
            </div>
        </div>
    </div>
    
    <div class="report-generation-info">
        Generated on {{ $reportCard->generated_date ? $reportCard->generated_date->format('F d, Y') : $reportCard->created_at->format('F d, Y') }}
        @if($reportCard->generator)
            by {{ $reportCard->generator->name }}
        @endif
    </div>
    
</div>
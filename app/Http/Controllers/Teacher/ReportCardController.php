<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ReportCard;
use App\Services\ReportCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportCardController extends Controller
{
    protected $reportCardService;

    public function __construct(ReportCardService $reportCardService)
    {
        $this->reportCardService = $reportCardService;
    }

    public function index()
    {
        $teacherId = Auth::id();
        
        // Get classes the teacher is teaching
        $classIds = \App\Models\Subject::where('teacher_id', $teacherId)
            ->pluck('class_id')
            ->unique()
            ->toArray();
        
        $classes = ClassModel::whereIn('id', $classIds)->get();
        
        $reportCards = ReportCard::with(['student.user', 'class'])
            ->whereIn('class_id', $classIds)
            ->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc')
            ->paginate(20);
        
        return view('teacher.report-cards.index', compact('reportCards', 'classes'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|string',
            'academic_year' => 'required|integer',
        ]);
        
        // Verify teacher teaches this class
        $teacherId = Auth::id();
        $hasAccess = \App\Models\Subject::where('teacher_id', $teacherId)
            ->where('class_id', $validated['class_id'])
            ->exists();
        
        if (!$hasAccess) {
            return back()->with('error', 'You are not authorized to generate report cards for this class.');
        }
        
        try {
            $reportCards = $this->reportCardService->generateForClass(
                $validated['class_id'],
                $validated['term'],
                $validated['academic_year']
            );
            
            return redirect()->route('teacher.report-cards.index')
                ->with('success', count($reportCards) . ' report cards generated successfully.');
        } catch (\Exception $e) {
            \Log::error('Report card generation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate report cards: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $reportCard = ReportCard::with(['student.user', 'class', 'generator'])
            ->findOrFail($id);
        
        // Verify access
        $teacherId = Auth::id();
        $hasAccess = \App\Models\Subject::where('teacher_id', $teacherId)
            ->where('class_id', $reportCard->class_id)
            ->exists();
        
        if (!$hasAccess) {
            abort(403, 'You are not authorized to view this report card.');
        }
        
        return view('teacher.report-cards.show', compact('reportCard'));
    }

    public function classReport(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|string',
            'academic_year' => 'required|integer',
        ]);
        
        $reportCards = ReportCard::with(['student.user'])
            ->where('class_id', $validated['class_id'])
            ->where('term', $validated['term'])
            ->where('academic_year', $validated['academic_year'])
            ->orderBy('position')
            ->get();
        
        $class = ClassModel::find($validated['class_id']);
        
        return view('teacher.report-cards.class-report', compact('reportCards', 'class'));
    }

    // ============ HELPER FOR AUTHORIZATION ============
    private function authorizeClassTeacher($reportCard)
    {
        $teacherId = Auth::id();
        
        // 1. Check if the teacher is the assigned Class Teacher
        $isClassTeacher = $reportCard->class->class_teacher_id === $teacherId;
        
        // 2. (Optional) Also allow if they teach any subject in this class
        $teachesClass = \App\Models\Subject::where('teacher_id', $teacherId)
            ->where('class_id', $reportCard->class_id)
            ->exists();

        if (!$isClassTeacher && !$teachesClass) {
            abort(403, 'You are not authorized to edit remarks for this class.');
        }
    }

    // ============ SINGLE EDIT ============
    public function editClassTeacherRemark($id)
    {
        $reportCard = \App\Models\ReportCard::with(['student.user', 'class'])->findOrFail($id);
        
        // Verify authorization
        $this->authorizeClassTeacher($reportCard);
        
        return view('teacher.report-cards.remark', compact('reportCard'));
    }

    public function updateClassTeacherRemark(Request $request, $id)
    {
        $reportCard = \App\Models\ReportCard::findOrFail($id);
        $this->authorizeClassTeacher($reportCard);

        $validated = $request->validate([
            'class_teacher_comment' => 'required|string|max:1000',
        ]);

        $reportCard->update([
            'class_teacher_comment' => $validated['class_teacher_comment'],
            // Auto-fill the teacher's name if it's empty
            'class_teacher_name' => $reportCard->class_teacher_name ?? Auth::user()->name,
        ]);

        return redirect()->route('teacher.report-cards.show', $reportCard->id)
            ->with('success', 'Class teacher remark updated successfully.');
    }

    // ============ BULK EDIT ============
    public function bulkRemarkForm(Request $request)
    {
        $teacherId = Auth::id();
        
        // Get classes where this teacher is the Class Teacher
        $classes = \App\Models\ClassModel::where('class_teacher_id', $teacherId)
            ->orWhereHas('subjects', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->orderBy('name')
            ->get();

        $reportCards = collect();
        $selectedClassId = $request->get('class_id');
        $selectedTerm = $request->get('term');
        $selectedYear = $request->get('academic_year');

        // If filters are applied, load the students
        if ($selectedClassId && $selectedTerm && $selectedYear) {
            $reportCards = \App\Models\ReportCard::with(['student.user'])
                ->where('class_id', $selectedClassId)
                ->where('term', $selectedTerm)
                ->where('academic_year', $selectedYear)
                ->get();
        }

        return view('teacher.report-cards.bulk-remark', compact(
            'classes', 'reportCards', 'selectedClassId', 'selectedTerm', 'selectedYear'
        ));
    }

    public function bulkRemarkStore(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|string',
            'academic_year' => 'required|integer',
            'class_teacher_comment' => 'required|string|max:1000',
        ]);

        // Verify teacher has access to this class
        $class = \App\Models\ClassModel::findOrFail($validated['class_id']);
        $teacherId = Auth::id();
        $hasAccess = $class->class_teacher_id === $teacherId || \App\Models\Subject::where('teacher_id', $teacherId)->where('class_id', $class->id)->exists();

        if (!$hasAccess) {
            return back()->with('error', 'You are not authorized for this class.');
        }

        // Update all report cards for this class/term/year
        $updated = \App\Models\ReportCard::where('class_id', $validated['class_id'])
            ->where('term', $validated['term'])
            ->where('academic_year', $validated['academic_year'])
            ->update([
                'class_teacher_comment' => $validated['class_teacher_comment'],
                'class_teacher_name' => $class->classTeacher->name ?? Auth::user()->name,
            ]);

        return redirect()->route('teacher.report-cards.index')
            ->with('success', "Bulk comment applied to {$updated} students.");
    }

    // ============ EDIT TRAITS ============
    public function editTraits($id)
    {
        $reportCard = \App\Models\ReportCard::with(['student.user', 'class'])->findOrFail($id);
        
        // Verify authorization (reuses the helper we made earlier)
        $this->authorizeClassTeacher($reportCard);
        
        // Define the default lists in case the report card is empty
        $affectiveList = [
            'Punctuality', 'Mental Alertness', 'Respect', 'Neatness', 'Politeness',
            'Honesty', 'Peer Relationship', 'Willingness To Learn', 'Teamwork',
        ];
        $psychomotorList = [
            'Verbal Skills', 'Sports & Games', 'Artistic Skills', 'Music',
            'Dance', 'Entrepreneurship', 'Chess',
        ];

        // Merge existing DB data with the list to ensure all fields show
        $affectiveTraits = $reportCard->affective_traits ?? [];
        $psychomotorSkills = $reportCard->psychomotor_skills ?? [];

        return view('teacher.report-cards.traits', compact(
            'reportCard', 'affectiveList', 'psychomotorList', 'affectiveTraits', 'psychomotorSkills'
        ));
    }

    public function updateTraits(Request $request, $id)
    {
        $reportCard = \App\Models\ReportCard::findOrFail($id);
        $this->authorizeClassTeacher($reportCard);

        // Validate that ratings are integers between 1 and 5
        $request->validate([
            'affective_traits' => 'nullable|array',
            'affective_traits.*' => 'nullable|integer|min:1|max:5',
            'psychomotor_skills' => 'nullable|array',
            'psychomotor_skills.*' => 'nullable|integer|min:1|max:5',
        ]);

        // Remove empty values to keep the JSON clean
        $affective = array_filter($request->input('affective_traits', []));
        $psychomotor = array_filter($request->input('psychomotor_skills', []));

        $reportCard->update([
            'affective_traits' => $affective,
            'psychomotor_skills' => $psychomotor,
        ]);

        return redirect()->route('teacher.report-cards.show', $reportCard->id)
            ->with('success', 'Affective traits and psychomotor skills updated successfully.');
    }

    // ============ BULK TRAITS ============
    public function bulkTraitsForm(Request $request)
    {
        $teacherId = Auth::id();
        
        // Get classes where this teacher is the Class Teacher
        $classes = \App\Models\ClassModel::where('class_teacher_id', $teacherId)
            ->orWhereHas('subjects', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->orderBy('name')
            ->get();

        $reportCards = collect();
        $selectedClassId = $request->get('class_id');
        $selectedTerm = $request->get('term');
        $selectedYear = $request->get('academic_year');

        // If filters are applied, load the students
        if ($selectedClassId && $selectedTerm && $selectedYear) {
            $reportCards = \App\Models\ReportCard::with(['student.user'])
                ->where('class_id', $selectedClassId)
                ->where('term', $selectedTerm)
                ->where('academic_year', $selectedYear)
                ->get();
        }

        $affectiveList = [
            'Punctuality', 'Mental Alertness', 'Respect', 'Neatness', 'Politeness',
            'Honesty', 'Peer Relationship', 'Willingness To Learn', 'Teamwork',
        ];
        $psychomotorList = [
            'Verbal Skills', 'Sports & Games', 'Artistic Skills', 'Music',
            'Dance', 'Entrepreneurship', 'Chess',
        ];

        return view('teacher.report-cards.bulk-traits', compact(
            'classes', 'reportCards', 'selectedClassId', 'selectedTerm', 'selectedYear',
            'affectiveList', 'psychomotorList'
        ));
    }

    public function bulkTraitsStore(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|string',
            'academic_year' => 'required|integer',
            'affective_traits' => 'nullable|array',
            'affective_traits.*' => 'nullable|integer|min:1|max:5',
            'psychomotor_skills' => 'nullable|array',
            'psychomotor_skills.*' => 'nullable|integer|min:1|max:5',
        ]);

        // Verify teacher has access to this class
        $class = \App\Models\ClassModel::findOrFail($validated['class_id']);
        $teacherId = Auth::id();
        $hasAccess = $class->class_teacher_id === $teacherId || \App\Models\Subject::where('teacher_id', $teacherId)->where('class_id', $class->id)->exists();

        if (!$hasAccess) {
            return back()->with('error', 'You are not authorized for this class.');
        }

        // Remove empty values
        $affective = array_filter($validated['affective_traits'] ?? []);
        $psychomotor = array_filter($validated['psychomotor_skills'] ?? []);

        if (empty($affective) && empty($psychomotor)) {
            return back()->with('error', 'Please provide at least one rating to apply.');
        }

        // Get all report cards for this class/term/year
        $reportCards = \App\Models\ReportCard::where('class_id', $validated['class_id'])
            ->where('term', $validated['term'])
            ->where('academic_year', $validated['academic_year'])
            ->get();

        $updatedCount = 0;

        foreach ($reportCards as $rc) {
            // Merge with existing data so we don't wipe out traits they already have
            $existingAffective = $rc->affective_traits ?? [];
            $existingPsychomotor = $rc->psychomotor_skills ?? [];

            $newAffective = array_merge($existingAffective, $affective);
            $newPsychomotor = array_merge($existingPsychomotor, $psychomotor);

            $rc->update([
                'affective_traits' => $newAffective,
                'psychomotor_skills' => $newPsychomotor,
            ]);
            $updatedCount++;
        }

        return redirect()->route('teacher.report-cards.index')
            ->with('success', "Bulk traits applied to {$updatedCount} students.");
    }

}
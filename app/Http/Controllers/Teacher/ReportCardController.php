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
}
<?php

namespace App\Http\Controllers\Admin;

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

    public function index(Request $request)
    {
        $query = ReportCard::with(['student.user', 'class'])
                          ->orderBy('academic_year', 'desc')
                          ->orderBy('term', 'desc');
        
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }
        
        if ($request->has('term') && $request->term) {
            $query->where('term', $request->term);
        }
        
        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }
        
        $reportCards = $query->paginate(20);
        $classes = ClassModel::orderBy('name')->get();
        
        return view('admin.report-cards.index', compact('reportCards', 'classes'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|string',
            'academic_year' => 'required|integer',
        ]);
        
        try {
            $reportCards = $this->reportCardService->generateForClass(
                $validated['class_id'],
                $validated['term'],
                $validated['academic_year']
            );
            
            return redirect()->route('admin.report-cards.index')
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
        
        return view('admin.report-cards.show', compact('reportCard'));
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
        
        return view('admin.report-cards.class-report', compact('reportCards', 'class'));
    }

    public function delete($id)
    {
        $reportCard = ReportCard::findOrFail($id);
        $reportCard->delete();
        
        return back()->with('success', 'Report card deleted successfully.');
    }
}
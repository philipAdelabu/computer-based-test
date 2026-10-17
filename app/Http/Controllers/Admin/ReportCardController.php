<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\ReportCard;
use App\Services\ReportCardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ReportCardAutoFillService;

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

    // app/Http/Controllers/Admin/ReportCardController.php
public function edit($id)
{
    $reportCard = ReportCard::with(['student.user', 'class'])->findOrFail($id);
    
    $defaultAffective = [
        'Punctuality', 'Mental Alertness', 'Respect', 'Neatness', 'Politeness',
        'Honesty', 'Peer Relationship', 'Willingness To Learn', 'Teamwork',
    ];
    
    $defaultPsychomotor = [
        'Verbal Skills', 'Sports & Games', 'Artistic Skills', 'Music',
        'Dance', 'Entrepreneurship', 'Chess',
    ];
    
    return view('admin.report-cards.edit', compact('reportCard', 'defaultAffective', 'defaultPsychomotor'));
}

public function update(Request $request, $id)
{
    $reportCard = ReportCard::findOrFail($id);
    
    $validated = $request->validate([
        'gender' => 'nullable|string|max:20',
        'age' => 'nullable|integer|min:1|max:100',
        'house' => 'nullable|string|max:50',
        'no_in_class' => 'nullable|integer|min:1',
        'session' => 'nullable|string|max:20',
        'max_class_score' => 'nullable|numeric',
        'min_class_score' => 'nullable|numeric',
        'marks_obtainable' => 'nullable|integer',
        'times_school_opened' => 'nullable|integer',
        'times_present' => 'nullable|integer',
        'class_teacher_name' => 'nullable|string|max:100',
        'class_teacher_comment' => 'nullable|string|max:500',
        'principal_name' => 'nullable|string|max:100',
        'principal_comment' => 'nullable|string|max:500',
        'vacation_date' => 'nullable|date',
        'resumption_date' => 'nullable|date',
        'affective_traits' => 'nullable|array',
        'psychomotor_skills' => 'nullable|array',
    ]);
    
    $reportCard->update($validated);
    
    return redirect()->route('admin.report-cards.show', $reportCard->id)
        ->with('success', 'Report card updated successfully.');
 }

  public function autoFill(Request $request, $id)
    {
        $reportCard = ReportCard::findOrFail($id);
        
        $overwrite = $request->input('overwrite', '0') === '1';
        
        try {
            $service = new ReportCardAutoFillService();
            $updates = $service->autoFill($reportCard, $overwrite);
            
            return redirect()->route('admin.report-cards.edit', $reportCard->id)
                ->with('success', 
                    'Auto-fill complete! ' . count($updates) . ' field(s) updated.'
                );
        } catch (\Exception $e) {
            \Log::error('Auto-fill failed: ' . $e->getMessage());
            return back()->with('error', 'Auto-fill failed: ' . $e->getMessage());
        }
    }

  
        public function bulkAutoFill(Request $request)
        {
            $validated = $request->validate([
                'class_id' => 'required|exists:classes,id',
                'term' => 'required|string',
                'academic_year' => 'required|integer',
                'overwrite' => 'nullable|boolean',
            ]);
            
            $overwrite = $request->boolean('overwrite');
            
            $reportCards = ReportCard::where('class_id', $validated['class_id'])
                ->where('term', $validated['term'])
                ->where('academic_year', $validated['academic_year'])
                ->get();
            
            if ($reportCards->isEmpty()) {
                return back()->with('error', 'No report cards found for the selected criteria.');
            }
            
            $service = new ReportCardAutoFillService();
            $updated = 0;
            $errors = 0;
            
            foreach ($reportCards as $reportCard) {
                try {
                    $service->autoFill($reportCard, $overwrite);
                    $updated++;
                } catch (\Exception $e) {
                    \Log::error("Bulk auto-fill failed for {$reportCard->id}: " . $e->getMessage());
                    $errors++;
                }
            }
            
            return redirect()->route('admin.report-cards.index')
                ->with('success', "Auto-filled {$updated} report card(s)." . 
                    ($errors > 0 ? " {$errors} failed." : '')
                );
        }

}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentAccessController extends Controller
{
    /**
     * Deactivate a student from taking assessments
     */
    public function deactivate(Request $request, $studentId)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'reactivate_at' => 'nullable|date|after:now',
        ]);

        $student = Student::findOrFail($studentId);

        try {
            $student->deactivateAssessments(
                $validated['reason'],
                Auth::id(),
                $validated['reactivate_at'] ? \Carbon\Carbon::parse($validated['reactivate_at']) : null
            );

            Log::info("Student {$student->id} deactivated by admin " . Auth::id(), [
                'reason' => $validated['reason'],
            ]);

            return redirect()->back()->with('success', 
                "{$student->user->name} has been deactivated from taking assessments."
            );
        } catch (\Exception $e) {
            Log::error('Failed to deactivate student: ' . $e->getMessage());
            return back()->with('error', 'Failed to deactivate student: ' . $e->getMessage());
        }
    }

    /**
     * Reactivate a student
     */
    public function reactivate($studentId)
    {
        $student = Student::findOrFail($studentId);

        try {
            $student->reactivateAssessments();

            Log::info("Student {$student->id} reactivated by admin " . Auth::id());

            return redirect()->back()->with('success', 
                "{$student->user->name} has been reactivated for assessments."
            );
        } catch (\Exception $e) {
            Log::error('Failed to reactivate student: ' . $e->getMessage());
            return back()->with('error', 'Failed to reactivate student: ' . $e->getMessage());
        }
    }

    /**
     * Bulk deactivate students
     */
    public function bulkDeactivate(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $count = Student::whereIn('id', $validated['student_ids'])
                ->update([
                    'can_take_assessments' => false,
                    'deactivation_reason' => $validated['reason'],
                    'deactivated_by' => Auth::id(),
                    'deactivated_at' => now(),
                ]);

            return redirect()->back()->with('success', 
                "{$count} student(s) deactivated from taking assessments."
            );
        } catch (\Exception $e) {
            Log::error('Bulk deactivation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to deactivate students.');
        }
    }

    /**
     * Bulk reactivate students
     */
    public function bulkReactivate(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        try {
            $count = Student::whereIn('id', $validated['student_ids'])
                ->update([
                    'can_take_assessments' => true,
                    'deactivation_reason' => null,
                    'deactivated_by' => null,
                    'deactivated_at' => null,
                    'reactivate_at' => null,
                ]);

            return redirect()->back()->with('success', 
                "{$count} student(s) reactivated for assessments."
            );
        } catch (\Exception $e) {
            Log::error('Bulk reactivation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to reactivate students.');
        }
    }

    /**
     * Show deactivation history for a student
     */
    public function history($studentId)
    {
        $student = Student::with(['user', 'deactivator'])->findOrFail($studentId);
        
        return view('admin.students.access-history', compact('student'));
    }
}
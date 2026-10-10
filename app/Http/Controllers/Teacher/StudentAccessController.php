<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentAccessController extends Controller
{
    /**
     * Check if teacher has access to this student
     */
    private function verifyTeacherAccess($studentId)
    {
        $teacherId = Auth::id();
        $student = Student::findOrFail($studentId);

        // Teacher can only manage students in their own classes
        $hasAccess = Subject::where('teacher_id', $teacherId)
            ->where('class_id', $student->class_id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You are not authorized to manage this student.');
        }

        return $student;
    }

    public function deactivate(Request $request, $studentId)
    {
        $student = $this->verifyTeacherAccess($studentId);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
            'reactivate_at' => 'nullable|date|after:now',
        ]);

        try {
            $student->deactivateAssessments(
                $validated['reason'],
                Auth::id(),
                $validated['reactivate_at'] ? \Carbon\Carbon::parse($validated['reactivate_at']) : null
            );

            return redirect()->back()->with('success', 
                "{$student->user->name} has been deactivated from taking assessments."
            );
        } catch (\Exception $e) {
            Log::error('Teacher deactivation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to deactivate student.');
        }
    }

    public function reactivate($studentId)
    {
        $student = $this->verifyTeacherAccess($studentId);

        try {
            $student->reactivateAssessments();
            return redirect()->back()->with('success', 
                "{$student->user->name} has been reactivated for assessments."
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to reactivate student.');
        }
    }
}
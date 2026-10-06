<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
   
   // app/Http/Controllers/Admin/AdminController.php
// Update the dashboard method

public function dashboard()
{
    $totalStudents = Student::count();
    $totalTeachers = User::where('role', 'teacher')->count();
    $totalClasses = ClassModel::count();
    $totalSubjects = Subject::count();
    $totalQuestions = Question::count();
    $totalTests = Exam::where('assessment_type', 'test')->count();
    $totalExams = Exam::where('assessment_type', 'exam')->count();
    $totalReportCards = ReportCard::count();
    
    // Recent assessments
    $recentAssessments = Exam::with(['subject', 'creator'])
                             ->orderBy('created_at', 'desc')
                             ->limit(5)
                             ->get();
    
    // Active assessments
    $now = \Carbon\Carbon::now(config('app.timezone'));
    $activeAssessments = Exam::where('is_published', true)
        ->where('status', 'active')
        ->where(function($query) use ($now) {
            $query->where('schedule_type', 'no_date')
                ->orWhere(function($q) use ($now) {
                    $q->where('schedule_type', 'single_date')
                        ->where('start_date', '<=', $now)
                        ->where(function($sub) use ($now) {
                            $sub->whereNull('end_date')
                                ->orWhere('end_date', '>=', $now);
                        });
                })
                ->orWhere(function($q) use ($now) {
                    $q->where('schedule_type', 'date_range')
                        ->where('available_from', '<=', $now)
                        ->where('available_to', '>=', $now);
                });
        })
        ->with('subject')
        ->limit(5)
        ->get();
    
    return view('admin.dashboard', compact(
        'totalStudents', 'totalTeachers', 'totalClasses',
        'totalSubjects', 'totalQuestions', 'totalTests',
        'totalExams', 'totalReportCards', 'recentAssessments',
        'activeAssessments'
    ));
}

    // Teacher Management
    public function teachers()
    {
        $teachers = User::where('role', 'teacher')->paginate(10);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function createTeacher()
    {
        return view('admin.teachers.create');
    }

    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);

        return redirect()->route('admin.teachers')
            ->with('success', 'Teacher created successfully.');
    }

    public function editTeacher($id)
    {
        $teacher = User::findOrFail($id);
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function updateTeacher(Request $request, $id)
    {
        $teacher = User::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $teacher->update($validated);

        if ($request->filled('password')) {
            $teacher->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.teachers')
            ->with('success', 'Teacher updated successfully.');
    }

    public function deleteTeacher($id)
    {
        $teacher = User::findOrFail($id);
        $teacher->delete();
        
        return redirect()->route('admin.teachers')
            ->with('success', 'Teacher deleted successfully.');
    }

    // Student Management
    public function students()
    {
        $students = Student::with(['user', 'class'])->paginate(10);
        return view('admin.students.index', compact('students'));
    }

    // app/Http/Controllers/Admin/AdminController.php
// Add this method if not already present

public function getStudentsByClass($classId)
{
    try {
        // Verify class exists
        $class = ClassModel::find($classId);
        if (!$class) {
            return response()->json(['error' => 'Class not found'], 404);
        }

        // Get active students with their user details
        $students = Student::where('class_id', $classId)
                          ->where('status', 'active')
                          ->with('user')
                          ->get(['id', 'user_id', 'admission_number', 'class_id']);

        // Format the response
        $formattedStudents = $students->map(function($student) {
            return [
                'id' => $student->id,
                'user_id' => $student->user_id,
                'name' => $student->user->name ?? 'Unknown',
                'admission_number' => $student->admission_number,
                'email' => $student->user->email ?? 'No email',
            ];
        });

        return response()->json($formattedStudents);
        
    } catch (\Exception $e) {
        \Log::error('Error fetching students: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to load students'], 500);
    }
}

    public function createStudent()
    {
        $classes = ClassModel::all();
        return view('admin.students.create', compact('classes'));
    }

    public function storeStudent(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'admission_number' => 'required|unique:students',
            'class_id' => 'required|exists:classes,id',
            'date_of_birth' => 'nullable|date',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        Student::create([
            'user_id' => $user->id,
            'admission_number' => $validated['admission_number'],
            'class_id' => $validated['class_id'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'guardian_name' => $validated['guardian_name'] ?? null,
            'guardian_phone' => $validated['guardian_phone'] ?? null,
        ]);

        return redirect()->route('admin.students')
            ->with('success', 'Student created successfully.');
    }

    public function editStudent($id)
    {
        $student = Student::with('user')->findOrFail($id);
        $classes = ClassModel::all();
        return view('admin.students.edit', compact('student', 'classes'));
    }

    public function updateStudent(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->user_id,
            'class_id' => 'required|exists:classes,id',
            'date_of_birth' => 'nullable|date',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'status' => 'required|in:active,graduated,suspended',
        ]);

        $student->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($request->filled('password')) {
            $student->user->update(['password' => Hash::make($request->password)]);
        }

        $student->update([
            'class_id' => $validated['class_id'],
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'guardian_name' => $validated['guardian_name'] ?? null,
            'guardian_phone' => $validated['guardian_phone'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.students')
            ->with('success', 'Student updated successfully.');
    }

    public function deleteStudent($id)
    {
        $student = Student::findOrFail($id);
        $student->user->delete();
        $student->delete();
        
        return redirect()->route('admin.students')
            ->with('success', 'Student deleted successfully.');
    }

    // Class Management
    public function classes()
    {
        $classes = ClassModel::withCount(['students', 'subjects'])->paginate(10);
        return view('admin.classes.index', compact('classes'));
    }

    public function createClass()
    {
        return view('admin.classes.create');
    }

    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:classes',
            'description' => 'nullable|string',
        ]);

        ClassModel::create($validated);

        return redirect()->route('admin.classes')
            ->with('success', 'Class created successfully.');
    }

    public function editClass($id)
    {
        $class = ClassModel::findOrFail($id);
        return view('admin.classes.edit', compact('class'));
    }

    public function updateClass(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:classes,code,' . $id,
            'description' => 'nullable|string',
        ]);

        $class->update($validated);

        return redirect()->route('admin.classes')
            ->with('success', 'Class updated successfully.');
    }

    public function deleteClass($id)
    {
        $class = ClassModel::findOrFail($id);
        $class->delete();
        
        return redirect()->route('admin.classes')
            ->with('success', 'Class deleted successfully.');
    }



 public function subjects()
    {
        $subjects = Subject::with(['class', 'teacher'])
                          ->orderBy('name')
                          ->paginate(15);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function createSubject()
    {
        $classes = ClassModel::all();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('admin.subjects.create', compact('classes', 'teachers'));
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects|max:50',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Verify that the teacher exists and has teacher role
        $teacher = User::findOrFail($validated['teacher_id']);
        if ($teacher->role !== 'teacher') {
            return back()->with('error', 'Selected user is not a teacher.');
        }

        Subject::create($validated);

        return redirect()->route('admin.subjects')
            ->with('success', 'Subject created successfully.');
    }

    public function editSubject($id)
    {
        $subject = Subject::findOrFail($id);
        $classes = ClassModel::all();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('admin.subjects.edit', compact('subject', 'classes', 'teachers'));
    }

    public function updateSubject(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $id,
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Verify that the teacher exists and has teacher role
        $teacher = User::findOrFail($validated['teacher_id']);
        if ($teacher->role !== 'teacher') {
            return back()->with('error', 'Selected user is not a teacher.');
        }

        $subject->update($validated);

        return redirect()->route('admin.subjects')
            ->with('success', 'Subject updated successfully.');
    }

    public function deleteSubject($id)
    {
        $subject = Subject::findOrFail($id);
        
        // Check if subject has questions or exams before deleting
        if ($subject->questions()->count() > 0) {
            return back()->with('error', 'Cannot delete subject with existing questions. Please delete questions first.');
        }
        
        if ($subject->exams()->count() > 0) {
            return back()->with('error', 'Cannot delete subject with existing exams. Please delete exams first.');
        }

        $subject->delete();

        return redirect()->route('admin.subjects')
            ->with('success', 'Subject deleted successfully.');
    }

    public function assignSubjectForm()
    {
        $classes = ClassModel::all();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $subjects = Subject::with(['class', 'teacher'])->get();
        return view('admin.subjects.assign', compact('classes', 'teachers', 'subjects'));
    }

    public function assignSubject(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $subject = Subject::findOrFail($validated['subject_id']);
        
        // Verify teacher role
        $teacher = User::findOrFail($validated['teacher_id']);
        if ($teacher->role !== 'teacher') {
            return back()->with('error', 'Selected user is not a teacher.');
        }

        $subject->update([
            'class_id' => $validated['class_id'],
            'teacher_id' => $validated['teacher_id'],
        ]);

        return redirect()->route('admin.subjects')
            ->with('success', 'Subject assigned successfully.');
    }

    public function bulkAssignForm()
    {
        $classes = ClassModel::all();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        return view('admin.subjects.bulk-assign', compact('classes', 'teachers'));
    }

    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:users,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        // Verify teacher role
        $teacher = User::findOrFail($validated['teacher_id']);
        if ($teacher->role !== 'teacher') {
            return back()->with('error', 'Selected user is not a teacher.');
        }

        // Update all selected subjects
        Subject::whereIn('id', $validated['subject_ids'])->update([
            'class_id' => $validated['class_id'],
            'teacher_id' => $validated['teacher_id'],
        ]);

        $count = count($validated['subject_ids']);
        return redirect()->route('admin.subjects')
            ->with('success', "{$count} subjects assigned successfully.");
    }

    public function getSubjectsByClass($classId)
    {
        $subjects = Subject::where('class_id', $classId)
                          ->where('status', 'active')
                          ->get(['id', 'name', 'code']);
        return response()->json($subjects);
    }

    public function getSubjectsByTeacher($teacherId)
    {
        $subjects = Subject::where('teacher_id', $teacherId)
                          ->where('status', 'active')
                          ->get(['id', 'name', 'code']);
        return response()->json($subjects);
    }

    public function subjectStats()
    {
        $totalSubjects = Subject::count();
        $activeSubjects = Subject::where('status', 'active')->count();
        $inactiveSubjects = Subject::where('status', 'inactive')->count();
        $subjectsWithQuestions = Subject::has('questions')->count();
        $subjectsWithExams = Subject::has('exams')->count();
        
        return view('admin.subjects.stats', compact(
            'totalSubjects', 'activeSubjects', 'inactiveSubjects',
            'subjectsWithQuestions', 'subjectsWithExams'
        ));
    }

    // app/Http/Controllers/Admin/AdminController.php
// Replace the admitStudents and processAdmission methods with these:

public function admitStudents()
{
    // Get all active students grouped by class
    $classes = ClassModel::with(['students' => function($query) {
        $query->where('status', 'active')->with('user');
    }])->get();
    
    // Get all classes for the dropdown (next class selection)
    $allClasses = ClassModel::all();
    
    return view('admin.classes.admit', compact('classes', 'allClasses'));
}



public function processAdmission(Request $request)
{
    $validated = $request->validate([
        'student_ids' => 'required|array|min:1',
        'student_ids.*' => 'exists:students,id',
        'current_class_id' => 'required|exists:classes,id',
        'next_class_id' => 'required|exists:classes,id|different:current_class_id',
    ]);

    // Get the next class
    $nextClass = ClassModel::findOrFail($validated['next_class_id']);
    
    // Update selected students
    $updatedCount = Student::whereIn('id', $validated['student_ids'])
                          ->where('class_id', $validated['current_class_id'])
                          ->update([
                              'class_id' => $validated['next_class_id'],
                              'status' => 'active' // Keep them active in the new class
                          ]);

    if ($updatedCount === 0) {
        return back()->with('error', 'No students were admitted. Please check your selection.');
    }

    return redirect()->route('admin.classes')
        ->with('success', "{$updatedCount} student(s) have been successfully admitted to {$nextClass->name}.");
}

}

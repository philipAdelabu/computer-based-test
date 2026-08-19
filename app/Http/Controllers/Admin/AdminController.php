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
    public function dashboard()
    {
        $totalStudents = Student::count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalClasses = ClassModel::count();
        $totalSubjects = Subject::count();
        $totalExams = Exam::count();
        
        return view('admin.dashboard', compact(
            'totalStudents', 'totalTeachers', 'totalClasses', 
            'totalSubjects', 'totalExams'
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

    public function admitStudents($classId)
    {
        $class = ClassModel::findOrFail($classId);
        $students = Student::where('class_id', $classId)
                          ->where('status', 'active')
                          ->get();
        
        return view('admin.classes.admit', compact('class', 'students'));
    }

    public function processAdmission(Request $request, $classId)
    {
        $class = ClassModel::findOrFail($classId);
        
        Student::where('class_id', $classId)
               ->update(['status' => 'graduated']);
        
        return redirect()->route('admin.classes')
            ->with('success', 'Students have been admitted to the next class.');
    }
}

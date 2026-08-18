<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Staff;
use App\Models\User;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Department;
use App\Models\Position;
use App\Models\StudentHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentStaffController extends Controller
{
    public function students(Request $request)
    {
        $query = Student::with(['user', 'schoolClass']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('classification')) {
            $query->where('classification', $request->classification);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(15);
        $classes = SchoolClass::all();

        return view('students.index', compact('students', 'classes'));
    }

    public function createStudent()
    {
        $classes = SchoolClass::all();
        return view('students.create', compact('classes'));
    }

    public function storeStudent(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'admission_number' => 'required|string|unique:students,admission_number',
            'class_id' => 'required|exists:classes,id',
            'classification' => 'required|in:day,boarding',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email|max:255',
        ]);

        // Create User
        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make('password'), // default password
            'status' => User::STATUS_ACTIVE,
        ]);

        // Assign student role using Spatie
        $user->assignRole('student');

        // Create Student
        $student = Student::create([
            'user_id' => $user->id,
            'admission_number' => $request->admission_number,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'class_id' => $request->class_id,
            'classification' => $request->classification,
            'status' => Student::STATUS_ACTIVE,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'guardian_email' => $request->guardian_email,
        ]);

        // Student Account setup automatically
        $student->account()->create([
            'balance' => 0,
            'total_invoiced' => 0,
            'total_paid' => 0,
        ]);

        StudentHistory::create([
            'student_id' => $student->id,
            'action' => 'Admission',
            'details' => 'Student admitted to ' . $student->schoolClass->name . ' as ' . str_replace('_', ' ', $student->classification),
        ]);

        return redirect()->route('students.index')->with('success', 'Student registered successfully.');
    }

    public function staff(Request $request)
    {
        $query = Staff::with(['user', 'position', 'department']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('staff_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $staff = $query->paginate(15);
        $departments = Department::all();

        return view('staff.index', compact('staff', 'departments'));
    }

    public function createStaff()
    {
        $departments = Department::all();
        $positions = Position::all();
        $roles = Role::where('slug', '!=', 'student')->get();
        return view('staff.create', compact('departments', 'positions', 'roles'));
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'staff_number' => 'required|string|unique:staff,staff_number',
            'role_id' => 'required|exists:roles,id',
            'position_id' => 'required|exists:positions,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user = User::create([
            'uuid' => (string) Str::uuid(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make('password'), // default password
            'status' => User::STATUS_ACTIVE,
        ]);

        // Assign role using Spatie
        $role = Role::findOrFail($request->role_id);
        $user->assignRole($role->name);

        Staff::create([
            'user_id' => $user->id,
            'staff_number' => $request->staff_number,
            'position_id' => $request->position_id,
            'department_id' => $request->department_id,
            'employment_status' => Staff::STATUS_FULL_TIME,
            'attendance_status' => 'present',
        ]);

        return redirect()->route('staff.index')->with('success', 'Staff registered successfully.');
    }

    // ── Student Show ──────────────────────────────────────────────────────────
    public function showStudent(Student $student)
    {
        $student->load([
            'user', 'schoolClass', 'histories',
            'activeAllocation.bed.room.dormitory',
            'account',
            'incidents' => fn($q) => $q->latest()->take(5),
            'movements' => fn($q) => $q->latest()->take(5),
        ]);
        $results = \App\Models\StudentResult::with(['assessment.teacherSubject.subject'])
            ->where('student_id', $student->id)->latest()->take(10)->get();
        return view('students.show', compact('student', 'results'));
    }

    // ── Student Edit ──────────────────────────────────────────────────────────
    public function editStudent(Student $student)
    {
        $classes = SchoolClass::all();
        return view('students.edit', compact('student', 'classes'));
    }

    // ── Student Update ────────────────────────────────────────────────────────
    public function updateStudent(Request $request, Student $student)
    {
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $student->user_id,
            'class_id'       => 'required|exists:classes,id',
            'classification' => 'required|in:day,boarding',
            'status'         => 'required|in:active,inactive,graduated,suspended,transferred',
            'guardian_name'  => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email|max:255',
        ]);

        $student->user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        if ($student->class_id != $request->class_id) {
            $newClass = SchoolClass::find($request->class_id);
            StudentHistory::create([
                'student_id' => $student->id,
                'action'     => 'Class Change',
                'details'    => 'Moved from ' . ($student->schoolClass->name ?? 'N/A') . ' to ' . ($newClass->name ?? 'N/A'),
            ]);
        }

        $student->update([
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'class_id'        => $request->class_id,
            'classification'  => $request->classification,
            'status'          => $request->status,
            'guardian_name'   => $request->guardian_name,
            'guardian_phone'  => $request->guardian_phone,
            'guardian_email'  => $request->guardian_email,
        ]);

        return redirect()->route('students.show', $student->id)->with('success', 'Student record updated successfully.');
    }

    // ── Student Destroy ───────────────────────────────────────────────────────
    public function destroyStudent(Student $student)
    {
        StudentHistory::create([
            'student_id' => $student->id,
            'action'     => 'Record Deleted',
            'details'    => 'Student record removed by ' . auth()->user()->name,
        ]);
        $student->user->delete();
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student record deleted.');
    }

    // ── Staff Show ────────────────────────────────────────────────────────────
    public function showStaff(Staff $staff)
    {
        $staff->load(['user', 'position', 'department', 'teacherSubjects.subject', 'teacherSubjects.schoolClass']);
        return view('staff.show', compact('staff'));
    }

    // ── Staff Edit ────────────────────────────────────────────────────────────
    public function editStaff(Staff $staff)
    {
        $departments = Department::all();
        $positions   = Position::all();
        $roles       = Role::where('slug', '!=', 'student')->get();
        return view('staff.edit', compact('staff', 'departments', 'positions', 'roles'));
    }

    // ── Staff Update ──────────────────────────────────────────────────────────
    public function updateStaff(Request $request, Staff $staff)
    {
        $request->validate([
            'first_name'        => 'required|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $staff->user_id,
            'phone'             => 'nullable|string|max:20',
            'position_id'       => 'required|exists:positions,id',
            'department_id'     => 'required|exists:departments,id',
            'employment_status' => 'required|in:full_time,part_time,contract',
            'attendance_status' => 'required|in:present,absent,on_leave',
        ]);

        $staff->user->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
        ]);

        $staff->update([
            'position_id'       => $request->position_id,
            'department_id'     => $request->department_id,
            'employment_status' => $request->employment_status,
            'attendance_status' => $request->attendance_status,
        ]);

        return redirect()->route('staff.show', $staff->id)->with('success', 'Staff record updated successfully.');
    }

    // ── Staff Destroy ─────────────────────────────────────────────────────────
    public function destroyStaff(Staff $staff)
    {
        $staff->user->delete();
        $staff->delete();
        return redirect()->route('staff.index')->with('success', 'Staff record deleted.');
    }
}

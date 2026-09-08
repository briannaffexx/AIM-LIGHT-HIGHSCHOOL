<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DisciplineRecord;
use App\Models\Student;
use App\Models\Staff;

class DisciplineController extends Controller
{
    public function index(Request $request)
    {
        $query = DisciplineRecord::with(['student', 'recorder.user'])->latest();

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        // Apply student name or admission number search if provided
        if ($request->filled('student_search')) {
            $search = $request->input('student_search');
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('admission_number', 'like', "%{$search}%");
            });
        }

            if ($request->filled('incident_type')) {
            $query->where('incident_type', $request->incident_type);
        }

        $records  = $query->paginate(20);
        $students = Student::orderBy('first_name')->get();

        $totalIncidents = DisciplineRecord::count();
        $behaviorCount = DisciplineRecord::where('incident_type', 'behavior')->count();
        $academicCount = DisciplineRecord::where('incident_type', 'academic')->count();
        $attendanceCount = DisciplineRecord::where('incident_type', 'attendance')->count();
        $warningsTotal = DisciplineRecord::sum('warnings_issued');
        
        return view('discipline.index', compact('records', 'students', 'totalIncidents', 'behaviorCount', 'academicCount', 'attendanceCount', 'warningsTotal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'    => 'required|exists:students,id',
            'incident_type' => 'required|string',
            'details'       => 'required|string',
            'action_taken'  => 'required|string',
            'warnings_issued' => 'required|integer|min:0',
        ]);

        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        DisciplineRecord::create([
            'student_id'      => $request->student_id,
            'incident_type'   => $request->incident_type,
            'details'         => $request->details,
            'action_taken'    => $request->action_taken,
            'warnings_issued' => $request->warnings_issued ?? 0,
            'recorded_by'     => $staff->id,
        ]);

        return redirect()->route('discipline.index')
            ->with('success', 'Discipline record logged successfully.');
    }
}

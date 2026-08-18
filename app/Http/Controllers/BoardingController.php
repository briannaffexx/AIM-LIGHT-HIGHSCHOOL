<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\House;
use App\Models\Dormitory;
use App\Models\Room;
use App\Models\Bed;
use App\Models\Student;
use App\Models\BoardingAllocation;
use App\Models\BoardingAttendance;
use App\Models\StudentMovement;
use App\Models\MealSchedule;
use App\Models\BoardingIncident;
use App\Models\BoardingResource;
use App\Models\Term;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\Auth;

class BoardingController extends Controller
{
    public function rooms()
    {
        $houses = House::with(['dormitories.rooms.beds.allocations.student.schoolClass'])->get();

        $unallocatedStudents = Student::where('classification', Student::CLASSIFICATION_BOARDING)
            ->whereDoesntHave('allocations', function($query) {
                $query->whereNull('vacated_at');
            })
            ->get();

        return view('boarding.rooms', compact('houses', 'unallocatedStudents'));
    }

    public function allocateBed(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'bed_id' => 'required|exists:beds,id',
        ]);

        $student = Student::findOrFail($request->student_id);
        if ($student->classification !== Student::CLASSIFICATION_BOARDING) {
            return redirect()->back()->withErrors(['student_id' => 'Only boarding students can be allocated a bed.']);
        }

        $bed = Bed::findOrFail($request->bed_id);
        if ($bed->status !== Bed::STATUS_AVAILABLE) {
            return redirect()->back()->withErrors(['bed_id' => 'This bed is not available.']);
        }

        // Vacate current active allocation if any
        $currentAllocation = BoardingAllocation::where('student_id', $request->student_id)
            ->whereNull('vacated_at')
            ->first();

        if ($currentAllocation) {
            $currentAllocation->update(['vacated_at' => now()]);

            // Update old bed status if no other active allocation for that bed
            $oldBed = $currentAllocation->bed;
            $activeOnOldBed = BoardingAllocation::where('bed_id', $oldBed->id)
                ->whereNull('vacated_at')
                ->exists();
            if (!$activeOnOldBed) {
                $oldBed->update(['status' => Bed::STATUS_AVAILABLE]);
            }
        }

        // Create new allocation
        BoardingAllocation::create([
            'student_id' => $request->student_id,
            'bed_id' => $request->bed_id,
            'allocated_at' => now(),
        ]);

        $bed->update(['status' => Bed::STATUS_OCCUPIED]);

        return redirect()->back()->with('success', 'Student allocated to bed successfully.');
    }

    public function vacateBed($allocationId)
    {
        $allocation = BoardingAllocation::findOrFail($allocationId);
        $allocation->update(['vacated_at' => now()]);

        // Update bed status if no active allocation remains for that bed
        $activeOnBed = BoardingAllocation::where('bed_id', $allocation->bed_id)
            ->whereNull('vacated_at')
            ->exists();

        if (!$activeOnBed) {
            $allocation->bed->update(['status' => Bed::STATUS_AVAILABLE]);
        }

        return redirect()->back()->with('success', 'Bed vacated successfully.');
    }

    public function attendance(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $type = $request->input('roll_call_type', 'evening');

        // Fetch boarding students with active allocation
        $students = Student::where('classification', Student::CLASSIFICATION_BOARDING)
            ->whereHas('allocations', function($q) {
                $q->whereNull('vacated_at');
            })
            ->with(['schoolClass', 'activeAllocation.bed.room.dormitory'])
            ->get();

        $existingAttendance = BoardingAttendance::where('date', $date)
            ->where('roll_call_type', $type)
            ->get()
            ->pluck('status', 'student_id');

        return view('boarding.attendance', compact('students', 'date', 'type', 'existingAttendance'));
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'roll_call_type' => 'required|in:morning,evening',
            'status' => 'required|array',
            'status.*' => 'required|in:present,absent,excused',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string',
        ]);

        foreach ($request->status as $studentId => $statusVal) {
            BoardingAttendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $request->date,
                    'roll_call_type' => $request->roll_call_type,
                ],
                [
                    'status' => $statusVal,
                    'remarks' => $request->remarks[$studentId] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Boarding attendance recorded successfully.');
    }

    public function movements()
    {
        $movements = StudentMovement::with(['student.schoolClass', 'approver.user'])
            ->latest()
            ->paginate(15);

        $boardingStudents = Student::where('classification', Student::CLASSIFICATION_BOARDING)->get();

        // Mark overdue movements automatically
        StudentMovement::where('status', StudentMovement::STATUS_APPROVED)
            ->where('expected_return_date', '<', now())
            ->whereNull('actual_return_date')
            ->update(['status' => StudentMovement::STATUS_OVERDUE]);

        return view('boarding.movements', compact('movements', 'boardingStudents'));
    }

    public function storeMovement(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'leave_type' => 'required|in:regular,emergency,weekend',
            'departure_date' => 'required|date',
            'expected_return_date' => 'required|date|after:departure_date',
        ]);

        $student = Student::findOrFail($request->student_id);
        if ($student->classification !== Student::CLASSIFICATION_BOARDING) {
            return redirect()->back()->withErrors(['student_id' => 'Only boarding students can have movement records.']);
        }

        StudentMovement::create([
            'student_id' => $request->student_id,
            'leave_type' => $request->leave_type,
            'departure_date' => $request->departure_date,
            'expected_return_date' => $request->expected_return_date,
            'status' => StudentMovement::STATUS_PENDING,
        ]);

        // Notify Head Teacher and Boarding Officer
        $student = Student::findOrFail($request->student_id);
        SystemNotification::notifyRole('Head Teacher', 'movement_request',
            'New Leave Request',
            $student->full_name . ' has requested ' . $request->leave_type . ' leave.',
            '/boarding/movements'
        );
        SystemNotification::notifyRole('Boarding Officer', 'movement_request',
            'New Leave Request',
            $student->full_name . ' has requested ' . $request->leave_type . ' leave.',
            '/boarding/movements'
        );

        return redirect()->back()->with('success', 'Leave request recorded. Awaiting approval.');
    }

    public function approveMovement($id)
    {
        $movement = StudentMovement::findOrFail($id);
        $staff = Auth::user()->staff;

        if (!$staff) {
            return redirect()->back()->withErrors(['error' => 'Only staff members can approve leaves.']);
        }

        $movement->update([
            'status' => StudentMovement::STATUS_APPROVED,
            'approved_by' => $staff->id,
        ]);

        // Notify boarding staff about approval
        $student = $movement->student;
        if ($student && $student->user_id) {
            SystemNotification::notifyUser($student->user_id, 'movement_approved',
                'Leave Approved',
                'Your ' . $movement->leave_type . ' leave request has been approved.',
                '/student/dashboard'
            );
        }
        SystemNotification::notifyRole('Boarding Officer', 'movement_approved',
            'Leave Approved',
            ($student->full_name ?? 'A student') . '\'s leave has been approved by ' . Auth::user()->name . '.',
            '/boarding/movements'
        );

        return redirect()->back()->with('success', 'Leave request approved successfully.');
    }

    public function departMovement($id)
    {
        $movement = StudentMovement::findOrFail($id);
        $movement->update([
            'status' => StudentMovement::STATUS_DEPARTED,
            'departure_date' => now(),
        ]);
        return redirect()->back()->with('success', 'Student departure recorded.');
    }

    public function returnMovement($id)
    {
        $movement = StudentMovement::findOrFail($id);
        $movement->update([
            'status' => StudentMovement::STATUS_RETURNED,
            'actual_return_date' => now(),
        ]);
        return redirect()->back()->with('success', 'Student return recorded.');
    }

    public function meals()
    {
        $term = Term::where('is_active', true)->first();
        $schedules = $term
            ? MealSchedule::where('term_id', $term->id)
                ->orderBy('day_of_week')
                ->orderBy('time')
                ->get()
            : collect();

        $days = [
            0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
            4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'
        ];

        return view('boarding.meals', compact('schedules', 'days'));
    }

    public function storeMeal(Request $request)
    {
        $term = Term::where('is_active', true)->first();
        if (!$term) {
            return redirect()->back()->withErrors(['term' => 'No active term configured.']);
        }

        $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'meal_type' => 'required|in:breakfast,lunch,dinner',
            'menu_item' => 'required|string|max:255',
            'time' => 'required|date_format:H:i',
        ]);

        MealSchedule::create([
            'term_id' => $term->id,
            'day_of_week' => $request->day_of_week,
            'meal_type' => $request->meal_type,
            'menu_item' => $request->menu_item,
            'time' => $request->time,
        ]);

        return redirect()->back()->with('success', 'Meal schedule added.');
    }

    public function incidents()
    {
        $incidents = BoardingIncident::with(['student.schoolClass', 'reporter.user'])
            ->latest('reported_at')
            ->paginate(15);

        $students = Student::where('classification', Student::CLASSIFICATION_BOARDING)->get();

        return view('boarding.incidents', compact('incidents', 'students'));
    }

    public function storeIncident(Request $request)
    {
        $staff = Auth::user()->staff;
        if (!$staff) {
            return redirect()->back()->withErrors(['error' => 'Only staff members can record incidents.']);
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'incident_type' => 'required|in:welfare,discipline,property,unauthorized_absence',
            'details' => 'required|string',
            'follow_up_actions' => 'nullable|string',
        ]);

        BoardingIncident::create([
            'student_id' => $request->student_id,
            'incident_type' => $request->incident_type,
            'details' => $request->details,
            'follow_up_actions' => $request->follow_up_actions,
            'reported_by' => $staff->id,
            'reported_at' => now(),
        ]);

        // Notify Head Teacher about the incident
        $student = Student::find($request->student_id);
        SystemNotification::notifyRole('Head Teacher', 'boarding_incident',
            'Boarding Incident Reported',
            ucfirst(str_replace('_', ' ', $request->incident_type)) . ' incident reported for ' . ($student->full_name ?? 'a student') . '.',
            '/boarding/incidents'
        );

        return redirect()->back()->with('success', 'Boarding incident recorded successfully.');
    }

    public function resources()
    {
        $resources = BoardingResource::latest()->paginate(15);
        return view('boarding.resources', compact('resources'));
    }

    public function storeResource(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:bed,mattress,locker,blanket,kitchen,dining,cleaning',
            'status' => 'required|in:available,in_use,maintenance,damaged',
            'notes' => 'nullable|string',
        ]);

        BoardingResource::create($request->all());

        return redirect()->back()->with('success', 'Boarding resource added.');
    }
}

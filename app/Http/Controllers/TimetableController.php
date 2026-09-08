<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Staff;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $classId = $request->get('class_id');

        $query = Timetable::with(['schoolClass', 'subject', 'teacher.user'])
            ->orderBy('day_of_week')
            ->orderBy('start_time');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $timetables = $query->get()->groupBy('day_of_week');
        $classes    = SchoolClass::orderBy('level')->get();
        $subjects   = Subject::orderBy('name')->get();
        $staff      = Staff::with('user')->get();

        return view('timetable.index', compact('timetables', 'classes', 'subjects', 'staff', 'classId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'class_id'       => 'required|exists:classes,id',
            'subject_id'     => 'required|exists:subjects,id',
            'staff_id'       => 'required|exists:staff,id',
            'day_of_week'    => 'required|integer|between:1,6',
            'start_time'     => 'required|date_format:H:i',
            'end_time'       => 'required|date_format:H:i|after:start_time',
            'timetable_type' => 'required|in:class,exam',
            'room_name'      => 'nullable|string|max:100',
        ]);

        Timetable::create($request->only([
            'class_id', 'subject_id', 'staff_id', 'day_of_week',
            'start_time', 'end_time', 'timetable_type', 'room_name',
        ]));

        return redirect()->route('timetable.index', ['class_id' => $request->class_id])
            ->with('success', 'Timetable slot added successfully.');
    }

    public function destroy(int $id)
    {
        Timetable::findOrFail($id)->delete();

        return redirect()->route('timetable.index')->with('success', 'Slot removed.');
    }
}

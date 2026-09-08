@extends('layouts.app')
@section('title', 'Timetable')
@section('page_title', 'Class & Exam Timetables')

@section('content')
<div class="dashboard-row" style="grid-template-columns: 2fr 1fr; align-items: start;">
    <!-- Timetable Grid -->
    <div class="glass-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-size:1.1rem; font-weight:700;">Weekly Schedule</h3>
            <form method="GET" style="display:flex; gap:0.5rem;">
                <select name="class_id" class="form-control" style="width:180px;" onchange="this.form.submit()">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @php
            $dayNames = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday'];
        @endphp

        @forelse($timetables as $day => $slots)
        <div style="margin-bottom:1.5rem;">
            <h4 style="font-size:0.85rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--primary-color); margin-bottom:0.75rem;">
                {{ $dayNames[$day] ?? 'Day '.$day }}
            </h4>
            <div style="display:flex; flex-wrap:wrap; gap:0.75rem;">
                @foreach($slots as $slot)
                <div class="glass-card" style="min-width:180px; padding:1rem; position:relative; border-color:rgba(99,102,241,0.2);">
                    <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:0.25rem;">
                        {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                    </div>
                    <div style="font-weight:700; font-size:0.9rem;">{{ $slot->subject->name ?? 'N/A' }}</div>
                    <div style="font-size:0.78rem; color:var(--text-secondary); margin-top:0.25rem;">{{ $slot->schoolClass->name ?? '' }}</div>
                    <div style="font-size:0.78rem; color:var(--text-secondary);">{{ $slot->teacher->user->full_name ?? '' }}</div>
                    @if($slot->room_name)
                        <span class="pill pill-info" style="margin-top:0.5rem; font-size:0.7rem;">{{ $slot->room_name }}</span>
                    @endif
                    <form action="{{ route('timetable.destroy', $slot->id) }}" method="POST" style="position:absolute; top:0.5rem; right:0.5rem;">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none; border:none; cursor:pointer; color:var(--danger-color); font-size:0.75rem;" title="Remove">✕</button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @empty
            <div style="text-align:center; color:var(--text-secondary); padding:2rem;">No timetable slots configured yet. Add some using the form.</div>
        @endforelse
    </div>

    <!-- Add Slot Form -->
    <div class="glass-card">
        <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">Add Timetable Slot</h3>
        <form action="{{ route('timetable.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Class *</label>
                <select name="class_id" class="form-control" required>
                    <option value="">Select class...</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Subject *</label>
                <select name="subject_id" class="form-control" required>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Teacher *</label>
                <select name="staff_id" class="form-control" required>
                    @foreach($staff as $t)
                        <option value="{{ $t->id }}">{{ $t->user->full_name ?? $t->staff_number }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Day of Week *</label>
                <select name="day_of_week" class="form-control" required>
                    @foreach([1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday'] as $num => $name)
                        <option value="{{ $num }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Start Time *</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">End Time *</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Type</label>
                <select name="timetable_type" class="form-control">
                    <option value="class">Class Lesson</option>
                    <option value="exam">Exam Session</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Room / Venue</label>
                <input type="text" name="room_name" class="form-control" placeholder="e.g. Room A1, Lab 2">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="14" x2="12" y2="18"/><line x1="10" y1="16" x2="14" y2="16"/></svg>
                <span>Add Timetable Slot</span>
            </button>
        </form>
    </div>
</div>
@endsection

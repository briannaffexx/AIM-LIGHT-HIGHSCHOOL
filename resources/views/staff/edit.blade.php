@extends('layouts.app')
@section('title', 'Edit Staff - AIM-LIGHT High School')
@section('page_title', 'Edit Staff Record')
@section('content')
    <div class="glass-card" style="max-width:700px;margin:0 auto;">
        <h3 style="font-size:1.15rem;font-weight:600;margin-bottom:1.5rem;border-bottom:1px solid var(--border-color);padding-bottom:1rem;">
            Editing: {{ $staff->user->name ?? 'Staff Member' }} &nbsp;<code style="font-size:0.8rem;">{{ $staff->staff_number }}</code>
        </h3>
        @if($errors->any())
            <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:1rem;margin-bottom:1.5rem;color:var(--danger-color);">
                <ul style="margin:0;padding-left:1.25rem;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form action="{{ route('staff.update', $staff->id) }}" method="POST">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $staff->user->first_name ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $staff->user->last_name ?? '') }}" required>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $staff->user->email ?? '') }}" required>
                </div>
                <div class="form-group">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $staff->user->phone ?? '') }}">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label for="position_id" class="form-label">Position *</label>
                    <select name="position_id" id="position_id" class="form-control" required>
                        @foreach($positions as $p)
                            <option value="{{ $p->id }}" {{ old('position_id', $staff->position_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="department_id" class="form-label">Department *</label>
                    <select name="department_id" id="department_id" class="form-control" required>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id', $staff->department_id) == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem;">
                <div class="form-group">
                    <label for="employment_status" class="form-label">Employment Status *</label>
                    <select name="employment_status" id="employment_status" class="form-control" required>
                        <option value="full_time" {{ old('employment_status', $staff->employment_status) == 'full_time' ? 'selected' : '' }}>Full Time</option>
                        <option value="part_time" {{ old('employment_status', $staff->employment_status) == 'part_time' ? 'selected' : '' }}>Part Time</option>
                        <option value="contract" {{ old('employment_status', $staff->employment_status) == 'contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="attendance_status" class="form-label">Attendance Status *</label>
                    <select name="attendance_status" id="attendance_status" class="form-control" required>
                        <option value="present" {{ old('attendance_status', $staff->attendance_status) == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ old('attendance_status', $staff->attendance_status) == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="on_leave" {{ old('attendance_status', $staff->attendance_status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;gap:1rem;justify-content:flex-end;">
                <a href="{{ route('staff.show', $staff->id) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Register Staff - Boarding School System')
@section('page_title', 'Register New Staff Member')

@section('content')
    <div class="glass-card" style="max-width: 650px; margin: 0 auto;">
        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Staff Registration Form</h3>

        <form action="{{ route('staff.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Full Name *</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Dr. Arthur Pendragon" value="{{ old('name') }}" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="e.g. arthur@school.com" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="staff_number" class="form-label">Staff Identification Code *</label>
                    <input type="text" name="staff_number" id="staff_number" class="form-control" placeholder="e.g. STF102" value="{{ old('staff_number') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="role_id" class="form-label">System Access Level / Role *</label>
                <select name="role_id" id="role_id" class="form-control" required>
                    <option value="">Select System Role</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }} ({{ $r->slug }})</option>
                    @endforeach
                </select>
                <span style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.25rem; display: block;">This controls what sections of the school system the staff member will be allowed to view/edit.</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="position_id" class="form-label">Job Title / Position *</label>
                    <select name="position_id" id="position_id" class="form-control" required>
                        <option value="">Select Position</option>
                        @foreach($positions as $p)
                            <option value="{{ $p->id }}" {{ old('position_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="department_id" class="form-label">Department *</label>
                    <select name="department_id" id="department_id" class="form-control" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('staff.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Register Staff</button>
            </div>
        </form>
    </div>
@endsection

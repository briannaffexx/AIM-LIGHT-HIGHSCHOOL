@extends('layouts.app')
@section('title', 'Edit Student - AIM-LIGHT High School')
@section('page_title', 'Edit Student Record')
@section('content')
    <div class="glass-card" style="max-width:700px;margin:0 auto;">
        <h3 style="font-size:1.15rem;font-weight:600;margin-bottom:1.5rem;border-bottom:1px solid var(--border-color);padding-bottom:1rem;">
            Editing: {{ $student->full_name }} &nbsp;<code style="font-size:0.8rem;">{{ $student->admission_number }}</code>
        </h3>
        @if($errors->any())
            <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:1rem;margin-bottom:1.5rem;color:var(--danger-color);">
                <ul style="margin:0;padding-left:1.25rem;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <form action="{{ route('students.update', $student->id) }}" method="POST">
            @csrf @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}" required>
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $student->user->email ?? '') }}" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label for="class_id" class="form-label">Class *</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id', $student->class_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="classification" class="form-label">Classification *</label>
                    <select name="classification" id="classification" class="form-control" required>
                        <option value="day" {{ old('classification', $student->classification) == 'day' ? 'selected' : '' }}>Day Scholar</option>
                        <option value="boarding" {{ old('classification', $student->classification) == 'boarding' ? 'selected' : '' }}>Boarding Student</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status" class="form-label">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                        <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                        <option value="transferred" {{ old('status', $student->status) == 'transferred' ? 'selected' : '' }}>Transferred</option>
                    </select>
                </div>
            </div>
            <h4 style="font-size:1rem;font-weight:600;margin:1.5rem 0 1rem 0;border-top:1px solid var(--border-color);padding-top:1.5rem;color:var(--text-secondary);">Guardian Information</h4>
            <div class="form-group">
                <label for="guardian_name" class="form-label">Guardian Full Name</label>
                <input type="text" name="guardian_name" id="guardian_name" class="form-control" value="{{ old('guardian_name', $student->guardian_name) }}">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:2rem;">
                <div class="form-group">
                    <label for="guardian_phone" class="form-label">Guardian Phone</label>
                    <input type="text" name="guardian_phone" id="guardian_phone" class="form-control" value="{{ old('guardian_phone', $student->guardian_phone) }}">
                </div>
                <div class="form-group">
                    <label for="guardian_email" class="form-label">Guardian Email</label>
                    <input type="email" name="guardian_email" id="guardian_email" class="form-control" value="{{ old('guardian_email', $student->guardian_email) }}">
                </div>
            </div>
            <div style="display:flex;gap:1rem;justify-content:flex-end;">
                <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
@endsection

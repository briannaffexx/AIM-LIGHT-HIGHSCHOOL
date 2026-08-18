@extends('layouts.app')

@section('title', 'Register Student - Boarding School System')
@section('page_title', 'Register New Student')

@section('content')
    <div class="glass-card" style="max-width: 700px; margin: 0 auto;">
        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Student Admission Form</h3>

        <form action="{{ route('students.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name *</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="e.g. John" value="{{ old('first_name') }}" required>
                </div>
                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="e.g. Doe" value="{{ old('last_name') }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="email" class="form-label">Student Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="e.g. john.doe@school.com" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label for="admission_number" class="form-label">Admission Number *</label>
                    <input type="text" name="admission_number" id="admission_number" class="form-control" placeholder="e.g. STD123" value="{{ old('admission_number') }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="class_id" class="form-label">Class Assignment *</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="classification" class="form-label">Boarding Classification *</label>
                    <select name="classification" id="classification" class="form-control" required>
                        <option value="day_scholar" {{ old('classification') == 'day_scholar' ? 'selected' : '' }}>Day Scholar</option>
                        <option value="boarding" {{ old('classification') == 'boarding' ? 'selected' : '' }}>Boarding Student</option>
                    </select>
                </div>
            </div>

            <h4 style="font-size: 1rem; font-weight: 600; margin: 1.5rem 0 1rem 0; border-top: 1px solid var(--border-color); padding-top: 1.5rem; color: var(--text-secondary);">Guardian Information</h4>

            <div class="form-group">
                <label for="guardian_name" class="form-label">Guardian Full Name</label>
                <input type="text" name="guardian_name" id="guardian_name" class="form-control" placeholder="e.g. Robert Doe" value="{{ old('guardian_name') }}">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label for="guardian_phone" class="form-label">Guardian Phone Number</label>
                    <input type="text" name="guardian_phone" id="guardian_phone" class="form-control" placeholder="e.g. +254712345678" value="{{ old('guardian_phone') }}">
                </div>
                <div class="form-group">
                    <label for="guardian_email" class="form-label">Guardian Email Address</label>
                    <input type="email" name="guardian_email" id="guardian_email" class="form-control" placeholder="e.g. guardian@gmail.com" value="{{ old('guardian_email') }}">
                </div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Admit Student</button>
            </div>
        </form>
    </div>
@endsection

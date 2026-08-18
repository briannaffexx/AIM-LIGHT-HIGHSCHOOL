@extends('layouts.app')

@section('title', 'Incidents - Boarding School System')
@section('page_title', 'Welfare & Incidents Report Log')

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Incidents List -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Incident Records</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Incident Type</th>
                            <th>Details</th>
                            <th>Action / Follow Up</th>
                            <th>Reported By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidents as $inc)
                            <tr>
                                <td><strong>{{ $inc->student->full_name }}</strong><br><small style="color: var(--text-secondary);">Class: {{ $inc->student->schoolClass->name ?? 'N/A' }}</small></td>
                                <td>
                                    <span class="pill @if($inc->incident_type == 'welfare') pill-info @elseif($inc->incident_type == 'unauthorized_absence') pill-danger @else pill-warning @endif" style="text-transform: capitalize;">
                                        {{ str_replace('_', ' ', $inc->incident_type) }}
                                    </span>
                                </td>
                                <td><span style="font-size: 0.85rem;">{{ $inc->details }}</span></td>
                                <td><span style="font-size: 0.85rem; color: #FFFFFF;">{{ $inc->follow_up_actions ?? '-' }}</span></td>
                                <td>{{ $inc->reporter->user->name ?? 'N/A' }}<br><small style="color: var(--text-secondary);">{{ $inc->reported_at->format('Y-m-d') }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No incidents logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1rem;">
                {{ $incidents->links() }}
            </div>
        </div>

        <!-- Log Incident Form -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">File Incident Report</h3>

            <form action="{{ route('boarding.incidents.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="student_id" class="form-label">Student involved *</label>
                    <select name="student_id" id="student_id" class="form-control" required>
                        <option value="">Select student...</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->admission_number }} - {{ $student->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="incident_type" class="form-label">Incident Category *</label>
                    <select name="incident_type" id="incident_type" class="form-control" required>
                        <option value="welfare">Welfare Concern</option>
                        <option value="discipline">Discipline Issue</option>
                        <option value="property">Property-related Damage</option>
                        <option value="unauthorized_absence">Unauthorized Absence</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="details" class="form-label">Incident Details *</label>
                    <textarea name="details" id="details" class="form-control" rows="4" placeholder="Describe the incident in detail..." required></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="follow_up_actions" class="form-label">Immediate / Follow-up Action</label>
                    <textarea name="follow_up_actions" id="follow_up_actions" class="form-control" rows="2" placeholder="e.g. parents called, suspended, warnings..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Record Incident File</button>
            </form>
        </div>
    </div>
@endsection

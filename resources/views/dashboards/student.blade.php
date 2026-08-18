@extends('layouts.app')

@section('title', 'Student Profile - Boarding School System')
@section('page_title', 'My Student Portal')

@section('content')
    <div class="dashboard-row">
        <!-- Student Info -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">My Profile Details</h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <label class="form-label" style="font-weight: 600;">Admission Number</label>
                    <div style="font-size: 1.1rem; color: #FFFFFF; font-weight: 700; margin-bottom: 1rem;">{{ $student->admission_number }}</div>

                    <label class="form-label" style="font-weight: 600;">Full Name</label>
                    <div style="font-size: 1rem; color: #FFFFFF; margin-bottom: 1rem;">{{ $student->full_name }}</div>

                    <label class="form-label" style="font-weight: 600;">Class / Form</label>
                    <div style="font-size: 1rem; color: #FFFFFF; margin-bottom: 1rem;">{{ $student->schoolClass->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <label class="form-label" style="font-weight: 600;">Classification</label>
                    <div style="font-size: 1rem; color: #FFFFFF; margin-bottom: 1rem;">
                        <span class="pill {{ $student->classification == 'boarding' ? 'pill-success' : 'pill-info' }}">
                            {{ str_replace('_', ' ', $student->classification) }}
                        </span>
                    </div>

                    <label class="form-label" style="font-weight: 600;">Guardian Info</label>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">
                        Name: {{ $student->guardian_name ?? 'N/A' }}<br>
                        Phone: {{ $student->guardian_phone ?? 'N/A' }}<br>
                        Email: {{ $student->guardian_email ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- Accommodation info if Boarder -->
            @if($student->classification == 'boarding')
                <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Dormitory Allocation</h4>
                    @if($allocation)
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                            <div>
                                <label class="form-label">House</label>
                                <div style="color: #FFFFFF;">{{ $allocation->bed->room->dormitory->house->name }}</div>
                            </div>
                            <div>
                                <label class="form-label">Dormitory</label>
                                <div style="color: #FFFFFF;">{{ $allocation->bed->room->dormitory->name }}</div>
                            </div>
                            <div>
                                <label class="form-label">Room</label>
                                <div style="color: #FFFFFF;">{{ $allocation->bed->room->name }}</div>
                            </div>
                            <div>
                                <label class="form-label">Bed Number</label>
                                <div style="color: #FFFFFF; font-weight: 600;">{{ $allocation->bed->bed_number }}</div>
                            </div>
                        </div>
                    @else
                        <p style="color: var(--warning-color); font-size: 0.9rem;">You are a boarding student but have not been allocated a bed yet. Please contact the Warden.</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Account Balance & Movements -->
        <div class="glass-card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">My Fee Account Status</h3>
                
                @if($account)
                    <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.5rem; text-align: center; margin-bottom: 1.5rem;">
                        <div class="metric-title" style="margin-bottom: 0.25rem;">Outstanding Balance</div>
                        <div class="metric-value {{ $account->balance > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 2.25rem; font-weight: 800; margin-bottom: 0;">
                            {{ number_format($account->balance, 2) }}
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.5rem;">
                            Total Billed: {{ number_format($account->total_invoiced, 2) }} | Cleared: {{ number_format($account->total_paid, 2) }}
                        </div>
                    </div>
                @else
                    <p style="color: var(--text-secondary);">No account ledger found.</p>
                @endif
            </div>

            <!-- Movement Request Form if Boarder -->
            @if($student->classification == 'boarding')
                <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                    <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 1rem;">Request Leave / Out of Bounds</h4>
                    
                    <form action="{{ route('boarding.movements.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        
                        <div class="form-group">
                            <label for="leave_type" class="form-label">Leave Type</label>
                            <select name="leave_type" id="leave_type" class="form-control" required>
                                <option value="weekend">Weekend Outing</option>
                                <option value="emergency">Emergency / Medical</option>
                                <option value="holiday">School Holiday</option>
                            </select>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1rem;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="departure_date" class="form-label">Departure</label>
                                <input type="datetime-local" name="departure_date" id="departure_date" class="form-control" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="expected_return_date" class="form-label">Expected Return</label>
                                <input type="datetime-local" name="expected_return_date" id="expected_return_date" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.6rem; font-size: 0.85rem;">Submit Request</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <div class="dashboard-row" style="grid-template-columns: 1.5fr 1fr;">
        <!-- Recent Results / Grades -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600;">My Recent Assessment Scores</h3>
                <a href="{{ route('academics.report-card', $student->id) }}" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.8rem;">View Term Report Card</a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Assessment</th>
                            <th>Marks Scored</th>
                            <th>Max Marks</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $res)
                            @php
                                $percent = $res->assessment->max_marks > 0 ? round(($res->marks_obtained / $res->assessment->max_marks) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td>{{ $res->assessment->teacherSubject->subject->name ?? 'N/A' }}</td>
                                <td>{{ $res->assessment->name }}</td>
                                <td><strong>{{ $res->marks_obtained }}</strong></td>
                                <td>{{ $res->assessment->max_marks }}</td>
                                <td>
                                    <span class="pill @if($percent >= 80) pill-success @elseif($percent >= 60) pill-info @elseif($percent >= 40) pill-warning @else pill-danger @endif">
                                        {{ $percent }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary);">No grades posted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Leaves / Movements History -->
        @if($student->classification == 'boarding')
            <div class="glass-card">
                <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">My Leave Request History</h3>
                
                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Expected Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_movements as $mvt)
                                <tr>
                                    <td><strong style="text-transform: capitalize;">{{ $mvt->leave_type }}</strong></td>
                                    <td>
                                        <span class="pill @if($mvt->status == 'pending') pill-warning @elseif($mvt->status == 'approved') pill-success @elseif($mvt->status == 'departed') pill-danger @else pill-info @endif">
                                            {{ $mvt->status }}
                                        </span>
                                    </td>
                                    <td>{{ $mvt->expected_return_date->format('M d, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; color: var(--text-secondary);">No leave requests logged.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection

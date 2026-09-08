@extends('layouts.app')

@section('title', 'Movements - Boarding School System')
@section('page_title', 'Student Leaves & Movements')

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Movements list -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Movements Ledger</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Type</th>
                            <th>Departure Date</th>
                            <th>Expected Return</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $mvt)
                            <tr>
                                <td><strong>{{ $mvt->student->full_name }}</strong><br><span style="font-size: 0.75rem; color: var(--text-secondary);">Adm: {{ $mvt->student->admission_number }}</span></td>
                                <td>{{ $mvt->student->schoolClass->name ?? 'N/A' }}</td>
                                <td><span class="pill pill-info">{{ $mvt->leave_type }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($mvt->departure_date)->format('M d, H:i') }}</td>
                                <td>
                                    @php
                                        $isOverdue = ($mvt->status === 'departed' && $mvt->expected_return_date < now());
                                    @endphp
                                    <span style="@if($isOverdue) color: var(--danger-color); font-weight: 700; @endif">
                                        {{ \Carbon\Carbon::parse($mvt->expected_return_date)->format('M d, H:i') }}
                                        @if($isOverdue) <br><small>[OVERDUE]</small> @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="pill @if($mvt->status == 'pending') pill-warning @elseif($mvt->status == 'approved') pill-success @elseif($mvt->status == 'departed') pill-danger @else pill-secondary @endif">
                                        {{ $mvt->status }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @if($mvt->status == 'pending')
                                            <form action="{{ route('boarding.movements.approve', $mvt->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-sm" title="Approve Leave">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                                    <span>Approve</span>
                                                </button>
                                            </form>
                                        @elseif($mvt->status == 'approved')
                                            <form action="{{ route('boarding.movements.depart', $mvt->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--info-color); border-color: rgba(6, 182, 212, 0.3);" title="Mark Departed">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                                    <span>Depart</span>
                                                </button>
                                            </form>
                                        @elseif($mvt->status == 'departed')
                                            <form action="{{ route('boarding.movements.return', $mvt->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--success-color); border-color: rgba(16, 185, 129, 0.3);" title="Mark Returned">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                                    <span>Return</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No movements logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1rem;">
                {{ $movements->links() }}
            </div>
        </div>

        <!-- Log Movement Form -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Log Leave Request</h3>

            <form action="{{ route('boarding.movements.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="student_id" class="form-label">Student *</label>
                    <select name="student_id" id="student_id" class="form-control" required>
                        <option value="">Select student...</option>
                        @foreach($boardingStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->admission_number }} - {{ $student->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="leave_type" class="form-label">Leave Category *</label>
                    <select name="leave_type" id="leave_type" class="form-control" required>
                        <option value="weekend">Weekend Outing</option>
                        <option value="emergency">Emergency / Medical</option>
                        <option value="holiday">School Holiday</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="departure_date" class="form-label">Scheduled Departure *</label>
                    <input type="datetime-local" name="departure_date" id="departure_date" class="form-control" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="expected_return_date" class="form-label">Expected Return *</label>
                    <input type="datetime-local" name="expected_return_date" id="expected_return_date" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    <span>Record Request</span>
                </button>
            </form>
        </div>
    </div>
@endsection

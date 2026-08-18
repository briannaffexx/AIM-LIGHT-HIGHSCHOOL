@extends('layouts.app')

@section('title', 'Head Teacher Dashboard - Boarding School System')
@section('page_title', 'Head Teacher Overview')

@section('content')
    <div class="card-grid">
        <div class="glass-card">
            <div class="metric-title">Total School Enrollment</div>
            <div class="metric-value">{{ $total_students }}</div>
            <div class="metric-indicator text-secondary">Active students</div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Leave Approvals Pending</div>
            <div class="metric-value">{{ $pending_leaves }}</div>
            <div class="metric-indicator {{ $pending_leaves > 0 ? 'indicator-down' : 'indicator-up' }}">
                Requires review
            </div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Procurement Pending</div>
            <div class="metric-value">{{ $pending_purchases }}</div>
            <div class="metric-indicator {{ $pending_purchases > 0 ? 'indicator-down' : 'indicator-up' }}">
                Awaiting authorization
            </div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Active Staff Members</div>
            <div class="metric-value">{{ $total_staff }}</div>
            <div class="metric-indicator text-secondary">Teaching and operations staff</div>
        </div>
    </div>

    <div class="dashboard-row">
        <!-- Leaves Awaiting Approval -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Leaves Awaiting Approval</h3>
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Type</th>
                            <th>Expected Return</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_leaves as $leave)
                            <tr>
                                <td>{{ $leave->student->full_name }}</td>
                                <td>{{ $leave->student->schoolClass->name ?? 'N/A' }}</td>
                                <td><span class="pill pill-warning">{{ $leave->leave_type }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($leave->expected_return_date)->format('M d, H:i') }}</td>
                                <td>
                                    <form action="{{ route('boarding.movements.approve', $leave->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Approve</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No leaves pending approval.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procurement Awaiting Authorization -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Procurement Awaiting Approval</h3>
            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_purchases as $req)
                            <tr>
                                <td>{{ $req->item_name }} (x{{ $req->quantity }})</td>
                                <td>{{ number_format($req->estimated_cost, 2) }}</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <form action="{{ route('finance.procurement.request.approve', $req->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">Approve</button>
                                        </form>
                                        <form action="{{ route('finance.procurement.request.reject', $req->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem; color: var(--danger-color); border-color: rgba(239, 68, 68, 0.2);">Reject</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No purchase requests pending.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

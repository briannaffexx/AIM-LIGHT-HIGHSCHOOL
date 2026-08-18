@extends('layouts.app')

@section('title', 'Boarding Overview - Boarding School System')
@section('page_title', 'Boarding Operations Dashboard')

@section('content')
    <div class="card-grid">
        <div class="glass-card">
            <div class="metric-title">Occupied Beds</div>
            <div class="metric-value">{{ $occupied_beds }}/{{ $total_beds }}</div>
            <div class="metric-indicator text-secondary">
                Occupancy Rate: {{ $occupancy_rate }}%
            </div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Vacant Beds</div>
            <div class="metric-value">{{ $vacant_beds }}</div>
            <div class="metric-indicator indicator-up">Beds available for allocation</div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Students Out on Leave</div>
            <div class="metric-value">{{ $active_leaves }}</div>
            <div class="metric-indicator text-secondary">Currently checked out</div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Overdue Returns</div>
            <div class="metric-value">{{ $overdue_leaves }}</div>
            <div class="metric-indicator {{ $overdue_leaves > 0 ? 'indicator-down' : 'text-secondary' }}">
                Late check-in alerts
            </div>
        </div>
    </div>

    <div class="dashboard-row">
        <!-- Active Student Movements / Leaves -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600;">Active Student Movements</h3>
                <a href="{{ route('boarding.movements') }}" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.8rem;">Manage Movements</a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Expected Return</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($active_movements as $mvt)
                            <tr>
                                <td><strong>{{ $mvt->student->full_name }}</strong></td>
                                <td><span class="pill pill-info">{{ $mvt->leave_type }}</span></td>
                                <td>
                                    <span class="pill @if($mvt->status == 'pending') pill-warning @elseif($mvt->status == 'approved') pill-success @elseif($mvt->status == 'departed') pill-danger @endif">
                                        {{ $mvt->status }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($mvt->expected_return_date)->format('M d, H:i') }}</td>
                                <td>
                                    <div style="display: flex; gap: 0.25rem;">
                                        @if($mvt->status === 'approved')
                                            <form action="{{ route('boarding.movements.depart', $mvt->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-primary" style="padding: 0.35rem 0.6rem; font-size: 0.7rem;">Check Out</button>
                                            </form>
                                        @elseif($mvt->status === 'departed')
                                            <form action="{{ route('boarding.movements.return', $mvt->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.7rem; color: var(--success-color);">Check In</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary);">No active movements.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Incidents -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600;">Recent Boarding Incidents</h3>
                <a href="{{ route('boarding.incidents') }}" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.8rem;">View All</a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Type</th>
                            <th>Reported By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_incidents as $inc)
                            <tr>
                                <td>{{ $inc->student->full_name }}</td>
                                <td><span class="pill pill-danger">{{ str_replace('_', ' ', $inc->incident_type) }}</span></td>
                                <td>{{ $inc->reporter->user->name ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-secondary);">No boarding incidents reported.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

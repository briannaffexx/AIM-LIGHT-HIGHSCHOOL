@extends('layouts.app')

@section('title', 'Boarding Overview - Boarding School System')
@section('page_title', 'Boarding Operations Dashboard')

@section('content')
    <div class="dashboard-row" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-bottom:1.75rem;">
        <div class="metric-card" style="border-left:4px solid #6366f1;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div class="metric-title">Occupied Beds</div>
                <div style="width:38px;height:38px;background:rgba(99,102,241,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
            </div>
            <div class="metric-value">{{ $occupied_beds }}<span style="font-size:1rem;color:var(--text-secondary);font-weight:600;">/{{ $total_beds }}</span></div>
            <div class="metric-sub">Occupancy: {{ $occupancy_rate }}%</div>
        </div>
        <div class="metric-card" style="border-left:4px solid #10b981;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div class="metric-title">Vacant Beds</div>
                <div style="width:38px;height:38px;background:rgba(16,185,129,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>
            <div class="metric-value">{{ $vacant_beds }}</div>
            <div class="metric-sub">Available for allocation</div>
        </div>
        <div class="metric-card" style="border-left:4px solid #f59e0b;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div class="metric-title">On Leave</div>
                <div style="width:38px;height:38px;background:rgba(245,158,11,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
                </div>
            </div>
            <div class="metric-value">{{ $active_leaves }}</div>
            <div class="metric-sub">Currently checked out</div>
        </div>
        <div class="metric-card" style="border-left:4px solid {{ $overdue_leaves > 0 ? '#ef4444' : '#10b981' }};">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div class="metric-title">Overdue Returns</div>
                <div style="width:38px;height:38px;background:rgba({{ $overdue_leaves > 0 ? '239,68,68' : '16,185,129' }},0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $overdue_leaves > 0 ? '#ef4444' : '#10b981' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
            </div>
            <div class="metric-value" style="color:{{ $overdue_leaves > 0 ? 'var(--danger-color)' : 'var(--success-color)' }};">{{ $overdue_leaves }}</div>
            <div class="metric-sub">Late check-in alerts</div>
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

@extends('layouts.app')

@section('title', 'Admin Dashboard - Boarding School System')
@section('page_title', 'System Administrator Dashboard')

@section('content')
    <!-- Dashboard Stats Grid -->
    <div class="card-grid">
        <div class="glass-card">
            <div class="metric-title">Total Students</div>
            <div class="metric-value">{{ $total_students }}</div>
            <div class="metric-indicator text-secondary">
                {{ $boarding_students }} Boarders | {{ $day_scholars }} Day Scholars
            </div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Staff Members</div>
            <div class="metric-value">{{ $total_staff }}</div>
            <div class="metric-indicator text-secondary">Registered teaching & non-teaching</div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Boarding Occupancy</div>
            <div class="metric-value">{{ $occupied_beds }}/{{ $total_beds }}</div>
            <div class="metric-indicator indicator-up">
                {{ $total_beds - $occupied_beds }} Vacant Beds
            </div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Fee Collection Rate</div>
            <div class="metric-value">
                @if($total_invoiced > 0)
                    {{ round(($total_collected / $total_invoiced) * 100, 1) }}%
                @else
                    0%
                @endif
            </div>
            <div class="metric-indicator text-secondary">
                Collected: {{ number_format($total_collected, 2) }}
            </div>
        </div>
    </div>

    <!-- Main Section Split -->
    <div class="dashboard-row">
        <!-- Recent Students -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600;">Recently Registered Students</h3>
                <a href="{{ route('students.index') }}" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.8rem;">View All</a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ADM NO</th>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Classification</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_students as $student)
                            <tr>
                                <td>{{ $student->admission_number }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->schoolClass->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="pill {{ $student->classification == 'boarding' ? 'pill-success' : 'pill-info' }}">
                                        {{ str_replace('_', ' ', $student->classification) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="pill pill-success">{{ $student->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary);">No students registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Staff -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600;">Staff Members</h3>
                <a href="{{ route('staff.index') }}" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.8rem;">View All</a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_staff as $staff)
                            <tr>
                                <td>{{ $staff->user->name ?? 'N/A' }}</td>
                                <td>{{ $staff->position->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="pill pill-success">{{ $staff->employment_status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-secondary);">No staff registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="dashboard-row" style="margin-top: 1.5rem;">
        <!-- Student Enrollment Pie Chart -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Student Enrollment Overview</h3>
            <div style="display: flex; align-items: center; justify-content: center; padding: 1rem;">
                <canvas id="enrollmentChart" width="260" height="260"></canvas>
            </div>
            <div style="display: flex; gap: 1.5rem; justify-content: center; margin-top: 1rem; font-size: 0.85rem;">
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#6366f1;margin-right:5px;"></span>Boarders ({{ $boarding_students }})</span>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#22d3ee;margin-right:5px;"></span>Day Scholars ({{ $day_scholars }})</span>
            </div>
        </div>

        <!-- Fee Collection Bar Chart -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Fee Summary (KES)</h3>
            <canvas id="feeChart" height="200"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const enrollCtx = document.getElementById('enrollmentChart');
if (enrollCtx) {
    new Chart(enrollCtx, {
        type: 'doughnut',
        data: {
            labels: ['Boarding Students', 'Day Scholars'],
            datasets: [{ data: [{{ $boarding_students }}, {{ $day_scholars }}], backgroundColor: ['#6366f1','#22d3ee'], borderColor:'transparent', hoverOffset:8 }]
        },
        options: { cutout:'65%', plugins:{ legend:{display:false} } }
    });
}
const feeCtx = document.getElementById('feeChart');
if (feeCtx) {
    new Chart(feeCtx, {
        type: 'bar',
        data: {
            labels: ['Invoiced','Collected','Expenses'],
            datasets: [{ label:'KES', data:[{{ $total_invoiced }},{{ $total_collected }},{{ $total_expenses }}], backgroundColor:['rgba(99,102,241,0.7)','rgba(16,185,129,0.7)','rgba(239,68,68,0.7)'], borderColor:['#6366f1','#10b981','#ef4444'], borderWidth:2, borderRadius:6 }]
        },
        options: { responsive:true, plugins:{legend:{display:false}}, scales:{ x:{grid:{color:'rgba(255,255,255,0.05)'},ticks:{color:'rgba(255,255,255,0.6)'}}, y:{grid:{color:'rgba(255,255,255,0.05)'},ticks:{color:'rgba(255,255,255,0.6)',callback:v=>'KES '+v.toLocaleString()}} } }
    });
}
</script>
@endpush

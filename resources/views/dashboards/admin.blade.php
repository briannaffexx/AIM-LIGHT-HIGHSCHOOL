@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'System Overview')

@section('content')

{{-- ── STAT CARDS ────────────────────────────────────────── --}}
<div class="dashboard-row" style="grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); margin-bottom:1.75rem;">

    <div class="metric-card" style="border-left:4px solid #6366f1;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Total Students</div>
            <div style="width:38px;height:38px;background:rgba(99,102,241,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $total_students }}</div>
        <div class="metric-sub">{{ $boarding_students }} Boarders &nbsp;·&nbsp; {{ $day_scholars }} Day Scholars</div>
    </div>

    <div class="metric-card" style="border-left:4px solid #10b981;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Staff Members</div>
            <div style="width:38px;height:38px;background:rgba(16,185,129,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $total_staff }}</div>
        <div class="metric-sub">Teaching &amp; non-teaching staff</div>
    </div>

    <div class="metric-card" style="border-left:4px solid #f59e0b;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Boarding Occupancy</div>
            <div style="width:38px;height:38px;background:rgba(245,158,11,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $occupied_beds }}<span style="font-size:1rem;color:var(--text-secondary);font-weight:600;">/{{ $total_beds }}</span></div>
        <div class="metric-sub">{{ $total_beds - $occupied_beds }} beds vacant</div>
    </div>

    <div class="metric-card" style="border-left:4px solid #ef4444;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Fee Collection Rate</div>
            <div style="width:38px;height:38px;background:rgba(239,68,68,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $total_invoiced > 0 ? round(($total_collected/$total_invoiced)*100,1) : 0 }}%</div>
        <div class="metric-sub">MWK {{ number_format($total_collected,0) }} collected</div>
    </div>

</div>

{{-- ── MAIN ROW: Recent Students + Staff ─────────────────── --}}
<div class="dashboard-row" style="grid-template-columns:2fr 1fr;margin-bottom:1.75rem;">

    {{-- Recent Students --}}
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div>
                <div style="font-size:1rem;font-weight:700;">Recently Registered Students</div>
                <div style="font-size:0.75rem;color:var(--text-secondary);margin-top:0.2rem;">Latest admissions across all classes</div>
            </div>
            <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                <span>View All</span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ADM #</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_students as $s)
                    <tr>
                        <td><code style="font-size:0.78rem;color:var(--primary-color);">{{ $s->admission_number }}</code></td>
                        <td><strong>{{ $s->full_name }}</strong></td>
                        <td style="color:var(--text-secondary);">{{ $s->schoolClass->name ?? 'N/A' }}</td>
                        <td><span class="pill {{ $s->classification == 'boarding' ? 'pill-info' : 'pill-success' }}">{{ ucfirst(str_replace('_',' ',$s->classification)) }}</span></td>
                        <td><span class="pill pill-success">{{ ucfirst($s->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--text-secondary);padding:2rem;">No students registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Staff Summary --}}
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
            <div>
                <div style="font-size:1rem;font-weight:700;">Staff Members</div>
                <div style="font-size:0.75rem;color:var(--text-secondary);margin-top:0.2rem;">Active staff on record</div>
            </div>
            <a href="{{ route('staff.index') }}" class="btn btn-secondary btn-sm">
                <span>View All</span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr><th>Name</th><th>Position</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($recent_staff as $st)
                    <tr>
                        <td><strong>{{ $st->user->first_name ?? '' }} {{ $st->user->last_name ?? '' }}</strong></td>
                        <td style="font-size:0.8rem;color:var(--text-secondary);">{{ $st->position->name ?? 'N/A' }}</td>
                        <td><span class="pill pill-success" style="font-size:0.68rem;">{{ ucfirst($st->employment_status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="3" style="text-align:center;color:var(--text-secondary);padding:2rem;">No staff on record.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── CHARTS ROW ─────────────────────────────────────────── --}}
<div class="dashboard-row" style="grid-template-columns:1fr 2fr;">

    {{-- Enrollment donut --}}
    <div class="glass-card" style="margin-bottom:0;display:flex;flex-direction:column;align-items:center;">
        <div style="width:100%;margin-bottom:1.25rem;">
            <div style="font-size:1rem;font-weight:700;">Enrollment Split</div>
            <div style="font-size:0.75rem;color:var(--text-secondary);margin-top:0.2rem;">Boarders vs Day Scholars</div>
        </div>
        <div style="position:relative;width:190px;height:190px;">
            <canvas id="enrollmentChart"></canvas>
            <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;">
                <div style="font-size:1.6rem;font-weight:800;">{{ $total_students }}</div>
                <div style="font-size:0.7rem;color:var(--text-secondary);font-weight:600;text-transform:uppercase;">Total</div>
            </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:0.5rem;margin-top:1.25rem;width:100%;">
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;"><span style="width:10px;height:10px;border-radius:3px;background:#6366f1;display:inline-block;"></span>Boarders</span>
                <strong style="font-size:0.82rem;">{{ $boarding_students }}</strong>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="display:flex;align-items:center;gap:0.5rem;font-size:0.82rem;"><span style="width:10px;height:10px;border-radius:3px;background:#22d3ee;display:inline-block;"></span>Day Scholars</span>
                <strong style="font-size:0.82rem;">{{ $day_scholars }}</strong>
            </div>
        </div>
    </div>

    {{-- Fee bar chart --}}
    <div class="glass-card" style="margin-bottom:0;">
        <div style="margin-bottom:1.25rem;">
            <div style="font-size:1rem;font-weight:700;">Financial Overview</div>
            <div style="font-size:0.75rem;color:var(--text-secondary);margin-top:0.2rem;">Invoiced · Collected · Expenses (MWK)</div>
        </div>
        <div style="position:relative;height:220px;">
            <canvas id="feeChart"></canvas>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
const isDark = !document.documentElement.classList.contains('light-theme');
const tickColor  = isDark ? 'rgba(148,163,184,0.8)' : 'rgba(100,116,139,0.9)';
const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

// Enrollment donut
const enrollCtx = document.getElementById('enrollmentChart');
if (enrollCtx) {
    new Chart(enrollCtx, {
        type: 'doughnut',
        data: {
            labels: ['Boarding','Day Scholars'],
            datasets: [{
                data: [{{ $boarding_students }}, {{ $day_scholars }}],
                backgroundColor: ['#6366f1','#22d3ee'],
                borderColor: 'transparent',
                borderRadius: 4,
                hoverOffset: 6,
            }]
        },
        options: {
            cutout: '70%',
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } } },
            animation: { animateScale: true }
        }
    });
}

// Fee bar chart
const feeCtx = document.getElementById('feeChart');
if (feeCtx) {
    new Chart(feeCtx, {
        type: 'bar',
        data: {
            labels: ['Invoiced','Collected','Expenses'],
            datasets: [{
                data: [{{ $total_invoiced }}, {{ $total_collected }}, {{ $total_expenses }}],
                backgroundColor: ['rgba(99,102,241,0.75)','rgba(16,185,129,0.75)','rgba(239,68,68,0.75)'],
                borderColor:     ['#6366f1','#10b981','#ef4444'],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: tickColor, font: { weight: '600' } } },
                y: { grid: { color: gridColor }, ticks: { color: tickColor, callback: v => 'MWK ' + v.toLocaleString() } }
            }
        }
    });
}
</script>
@endpush

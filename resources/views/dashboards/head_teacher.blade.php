@extends('layouts.app')

@section('title', 'Head Teacher Executive Dashboard — AIM-LIGHT High School')
@section('page_title', 'Head Teacher Executive Center')

@section('content')

{{-- ── EXECUTIVE ACTION TOOLBAR ─────────────────────────────── --}}
<div class="glass-card" style="margin-bottom: 1.75rem; border-left: 4px solid var(--primary-color);">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <div>
            <h3 style="font-size:1.15rem; font-weight:800; color:var(--text-primary);">
                Welcome, {{ Auth::user()->first_name ?? 'Principal' }}!
            </h3>
            <p style="font-size:0.82rem; color:var(--text-secondary); margin-top:0.25rem;">
                School Academic & Administrative Executive Portal · Term {{ date('Y') }}
            </p>
        </div>
        <div class="btn-group">
            <a href="{{ route('students.create') }}" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                <span>Admit Student</span>
            </a>
            <a href="{{ route('staff.create') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><line x1="19" y1="11" x2="19" y2="17"/><line x1="22" y1="14" x2="16" y2="14"/></svg>
                <span>Register Staff</span>
            </a>
            <a href="{{ route('communication.announcements') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                <span>Post Notice</span>
            </a>
            <a href="{{ route('timetable.index') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Timetable</span>
            </a>
        </div>
    </div>
</div>

{{-- ── STAT CARDS ────────────────────────────────────────── --}}
<div class="dashboard-row" style="grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); margin-bottom:1.75rem;">

    <!-- Total Students -->
    <div class="metric-card" style="border-left:4px solid #6366f1;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">School Enrollment</div>
            <div style="width:38px;height:38px;background:rgba(99,102,241,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $total_students }}</div>
        <div class="metric-sub">{{ $boarding_students }} Boarders &nbsp;·&nbsp; {{ $day_scholars }} Day Scholars</div>
    </div>

    <!-- Faculty & Staff -->
    <div class="metric-card" style="border-left:4px solid #10b981;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Active Staff</div>
            <div style="width:38px;height:38px;background:rgba(16,185,129,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $total_staff }}</div>
        <div class="metric-sub">{{ $total_classes }} Classes &nbsp;·&nbsp; {{ $total_subjects }} Subjects</div>
    </div>

    <!-- Pending Leaves -->
    <div class="metric-card" style="border-left:4px solid #f59e0b;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Leave Approvals</div>
            <div style="width:38px;height:38px;background:rgba(245,158,11,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
            </div>
        </div>
        <div class="metric-value" style="color:{{ $pending_leaves > 0 ? 'var(--warning-color)' : 'var(--text-primary)' }};">
            {{ $pending_leaves }}
        </div>
        <div class="metric-sub">{{ $pending_leaves > 0 ? 'Requires head review' : 'All leave requests cleared' }}</div>
    </div>

    <!-- Pending Procurement Requests -->
    <div class="metric-card" style="border-left:4px solid #ef4444;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Purchase Approvals</div>
            <div style="width:38px;height:38px;background:rgba(239,68,68,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
        </div>
        <div class="metric-value" style="color:{{ $pending_purchases > 0 ? 'var(--danger-color)' : 'var(--text-primary)' }};">
            {{ $pending_purchases }}
        </div>
        <div class="metric-sub">MWK {{ number_format($pending_purchases_cost, 2) }} total value</div>
    </div>

</div>

{{-- ── PENDING AUTHORIZATIONS DESK ───────────────────────── --}}
<div class="dashboard-row" style="grid-template-columns:1fr 1fr; margin-bottom:1.75rem;">

    <!-- Student Leave Requests Awaiting Head Teacher Sign-Off -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.5rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">Leave Requests Awaiting Sign-Off</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Pending boarding student gate passes</div>
            </div>
            <a href="{{ route('boarding.movements') }}" class="btn btn-secondary btn-sm">
                <span>View Ledger</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Type</th>
                        <th>Return Date</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_leaves as $leave)
                        <tr>
                            <td>
                                <strong>{{ $leave->student->full_name }}</strong><br>
                                <span style="font-size:0.75rem; color:var(--text-secondary);">Adm: {{ $leave->student->admission_number }}</span>
                            </td>
                            <td>{{ $leave->student->schoolClass->name ?? 'N/A' }}</td>
                            <td><span class="pill pill-warning" style="text-transform:capitalize;">{{ $leave->leave_type }}</span></td>
                            <td style="font-size:0.8rem;">{{ \Carbon\Carbon::parse($leave->expected_return_date)->format('M d, H:i') }}</td>
                            <td style="text-align:right;">
                                <form action="{{ route('boarding.movements.approve', $leave->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span>Authorize</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--text-secondary); padding:2rem;">
                                ✓ No leave requests pending sign-off.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Purchase Requests Awaiting Head Teacher Authorization -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.5rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">Purchase Requisitions Awaiting Approval</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Department expenditure authorizations</div>
            </div>
        </div>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Item & Qty</th>
                        <th>Requested By</th>
                        <th style="text-align:right;">Est. Cost</th>
                        <th style="text-align:right;">Decide</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_purchases as $req)
                        <tr>
                            <td>
                                <strong>{{ $req->item_name }}</strong><br>
                                <span style="font-size:0.75rem; color:var(--text-secondary);">Qty: {{ $req->quantity }}</span>
                            </td>
                            <td>{{ $req->requester->first_name ?? $req->requester->name ?? 'Staff' }}</td>
                            <td style="text-align:right; font-weight:700;">MWK {{ number_format($req->estimated_cost, 2) }}</td>
                            <td style="text-align:right;">
                                <div class="btn-group" style="justify-content:flex-end; gap:0.3rem;">
                                    <form action="{{ route('finance.procurement.request.approve', $req->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-icon-sm" title="Approve Request">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('finance.procurement.request.reject', $req->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary btn-icon-sm" style="color:var(--danger-color); border-color:rgba(239,68,68,0.25);" title="Reject Request">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color:var(--text-secondary); padding:2rem;">
                                ✓ No purchase requisitions awaiting decision.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── ACADEMIC & DISCIPLINE SURVEILLANCE ROW ─────────────── --}}
<div class="dashboard-row" style="grid-template-columns:2fr 1fr; margin-bottom:1.75rem;">

    <!-- Recent Admissions & Academic Link -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">Student Directory & Academic Tracking</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Recently enrolled students and report card links</div>
            </div>
            <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                <span>All Students</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Adm #</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Type</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_students as $st)
                        <tr>
                            <td><code style="color:var(--primary-color);">{{ $st->admission_number }}</code></td>
                            <td><strong>{{ $st->full_name }}</strong></td>
                            <td>{{ $st->schoolClass->name ?? 'N/A' }}</td>
                            <td>
                                <span class="pill {{ $st->classification == 'boarding' ? 'pill-info' : 'pill-success' }}">
                                    {{ ucfirst(str_replace('_',' ',$st->classification)) }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <div class="btn-group" style="justify-content:flex-end; gap:0.3rem;">
                                    <a href="{{ route('students.show', $st->id) }}" class="btn btn-secondary btn-icon-sm" title="View Profile">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </a>
                                    <a href="{{ route('academics.report-card', $st->id) }}" class="btn btn-secondary btn-icon-sm" title="Academic Report Card">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--text-secondary); padding:2rem;">No students registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Discipline Incident Alerts -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">Discipline Alerts</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Recent student conduct files</div>
            </div>
            <a href="{{ route('discipline.index') }}" class="btn btn-secondary btn-sm">
                <span>View All</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div style="display:flex; flex-direction:column; gap:0.75rem;">
            @forelse($recent_incidents as $inc)
                <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border-color); border-radius:10px; padding:0.85rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.35rem;">
                        <strong>{{ $inc->student->full_name ?? 'Student' }}</strong>
                        <span class="pill @if($inc->incident_type=='behavior') pill-danger @elseif($inc->incident_type=='academic') pill-warning @else pill-info @endif" style="font-size:0.65rem;">
                            {{ ucfirst($inc->incident_type) }}
                        </span>
                    </div>
                    <p style="font-size:0.8rem; color:var(--text-secondary); line-height:1.4; margin-bottom:0.4rem;">
                        {{ Str::limit($inc->details, 70) }}
                    </p>
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.72rem; color:var(--text-secondary);">
                        <span>Action: {{ Str::limit($inc->action_taken, 25) }}</span>
                        <span>{{ $inc->created_at->format('d M') }}</span>
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:var(--text-secondary); padding:2rem;">
                    ✓ Zero discipline records reported.
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── CLASS ENROLLMENT & RECENT NOTICES ──────────────────── --}}
<div class="dashboard-row" style="grid-template-columns:1fr 1fr;">

    <!-- Class Enrollment Matrix -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">Class Enrollment Roster</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Classroom distribution across academic streams</div>
            </div>
            <a href="{{ route('academics.teacher-subjects') }}" class="btn btn-secondary btn-sm">
                <span>Academics</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:0.75rem;">
            @forelse($classes as $cls)
                <div style="background:rgba(99,102,241,0.05); border:1px solid rgba(99,102,241,0.15); border-radius:12px; padding:1rem; text-align:center;">
                    <div style="font-size:0.8rem; font-weight:700; color:var(--primary-color);">{{ $cls->name }}</div>
                    <div style="font-size:1.5rem; font-weight:800; margin:0.35rem 0;">{{ $cls->students_count }}</div>
                    <div style="font-size:0.72rem; color:var(--text-secondary);">Enrolled Students</div>
                </div>
            @empty
                <div style="text-align:center; color:var(--text-secondary); padding:1.5rem; grid-column:1/-1;">
                    No classes set up yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Official School Notices Published -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">Active School Announcements</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Broadcasts sent to students, parents & staff</div>
            </div>
            <a href="{{ route('communication.announcements') }}" class="btn btn-secondary btn-sm">
                <span>All Notices</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div style="display:flex; flex-direction:column; gap:0.75rem;">
            @forelse($recent_announcements as $ann)
                <div style="border-bottom:1px solid var(--border-color); padding-bottom:0.75rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.25rem;">
                        <strong style="font-size:0.9rem;">{{ $ann->title }}</strong>
                        <span class="pill pill-info" style="font-size:0.65rem;">{{ ucfirst($ann->target_audience) }}</span>
                    </div>
                    <p style="font-size:0.8rem; color:var(--text-secondary); line-height:1.4;">
                        {{ Str::limit($ann->content, 90) }}
                    </p>
                    <div style="font-size:0.7rem; color:var(--text-secondary); margin-top:0.35rem;">
                        By {{ $ann->author->first_name ?? 'Admin' }} · {{ $ann->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:var(--text-secondary); padding:1.5rem;">
                    No recent announcements posted.
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

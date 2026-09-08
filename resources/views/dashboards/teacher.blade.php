@extends('layouts.app')

@section('title', 'Teacher Dashboard — AIM-LIGHT High School')
@section('page_title', 'Teacher Academic Hub')

@section('content')

{{-- ── TEACHER ACTION BAR ────────────────────────────────────── --}}
<div class="glass-card" style="margin-bottom: 1.75rem; border-left: 4px solid var(--primary-color);">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;">
        <div>
            <h3 style="font-size:1.15rem; font-weight:800; color:var(--text-primary);">
                Welcome back, {{ $staff->first_name ?? Auth::user()->first_name ?? 'Teacher' }}!
            </h3>
            <p style="font-size:0.82rem; color:var(--text-secondary); margin-top:0.25rem;">
                Faculty Member · {{ $staff->department->name ?? 'Academics Department' }} · Term {{ date('Y') }}
            </p>
        </div>
        <div class="btn-group">
            <a href="{{ route('academics.teacher-subjects') }}" class="btn btn-primary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                <span>My Subjects & Marks</span>
            </a>
            <a href="{{ route('timetable.index') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span>Class Timetable</span>
            </a>
            <a href="{{ route('discipline.index') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <span>Log Incident</span>
            </a>
            <a href="{{ route('library.books') }}" class="btn btn-secondary btn-sm">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                <span>Library</span>
            </a>
        </div>
    </div>
</div>

{{-- ── METRIC STAT CARDS ─────────────────────────────────────── --}}
<div class="dashboard-row" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); margin-bottom:1.75rem;">

    <!-- Classes Assigned -->
    <div class="metric-card" style="border-left:4px solid #6366f1;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Classes Assigned</div>
            <div style="width:38px;height:38px;background:rgba(99,102,241,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $classes_count }}</div>
        <div class="metric-sub">Teaching groups assigned</div>
    </div>

    <!-- Subjects Assigned -->
    <div class="metric-card" style="border-left:4px solid #10b981;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Subjects Taught</div>
            <div style="width:38px;height:38px;background:rgba(16,185,129,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $subjects_count }}</div>
        <div class="metric-sub">Curriculum streams</div>
    </div>

    <!-- Learners Taught -->
    <div class="metric-card" style="border-left:4px solid #f59e0b;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Enrolled Learners</div>
            <div style="width:38px;height:38px;background:rgba(245,158,11,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $my_students_count }}</div>
        <div class="metric-sub">Students across your classes</div>
    </div>

    <!-- Assessments Count -->
    <div class="metric-card" style="border-left:4px solid #22d3ee;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <div class="metric-title">Assessments</div>
            <div style="width:38px;height:38px;background:rgba(34,211,238,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22d3ee" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            </div>
        </div>
        <div class="metric-value">{{ $total_assessments }}</div>
        <div class="metric-sub">Tests, assignments & exams</div>
    </div>

</div>

{{-- ── MY TEACHING ROSTER & GRADEBOOK ───────────────────────── --}}
<div class="glass-card" style="margin-bottom: 1.75rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.5rem;">
        <div>
            <h3 style="font-size:1.05rem; font-weight:700;">My Assigned Subjects & Classes</h3>
            <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Direct access to syllabus, assessment creation, and marks entry</div>
        </div>
        <a href="{{ route('academics.teacher-subjects') }}" class="btn btn-secondary btn-sm">
            <span>Manage All</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Class Stream</th>
                    <th>Class Density</th>
                    <th>Assessments</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($my_subjects as $ms)
                    <tr>
                        <td><code style="color:var(--primary-color);">{{ $ms->subject->code ?? 'SUB' }}</code></td>
                        <td><strong>{{ $ms->subject->name ?? 'N/A' }}</strong></td>
                        <td><span class="pill pill-info">{{ $ms->schoolClass->name ?? 'N/A' }}</span></td>
                        <td style="font-size:0.85rem;">{{ $ms->schoolClass->students->count() ?? 0 }} learners</td>
                        <td>
                            <span class="pill {{ $ms->assessments->count() > 0 ? 'pill-success' : 'pill-warning' }}">
                                {{ $ms->assessments->count() }} created
                            </span>
                        </td>
                        <td style="text-align:right;">
                            <div class="btn-group" style="justify-content:flex-end;">
                                <a href="{{ route('academics.assessments', $ms->id) }}" class="btn btn-primary btn-sm">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                    <span>Assessments & Marks</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--text-secondary); padding:2rem;">
                            You are not assigned to teach any subjects yet. Contact the Head Teacher for subject allocations.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── SCHEDULE & RECENT ASSESSMENTS ROW ─────────────────────── --}}
<div class="dashboard-row" style="grid-template-columns:1fr 1fr; margin-bottom:1.75rem;">

    <!-- Teaching Timetable / Schedule -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">My Teaching Timetable</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Weekly scheduled class slots</div>
            </div>
            <a href="{{ route('timetable.index') }}" class="btn btn-secondary btn-sm">
                <span>Full Timetable</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div style="display:flex; flex-direction:column; gap:0.75rem;">
            @forelse($my_timetables->take(6) as $slot)
                <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border-color); border-radius:10px; padding:0.85rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
                    <div>
                        <div style="font-weight:700; font-size:0.9rem; color:var(--text-primary);">
                            {{ $slot->subject->name ?? 'Subject' }} · {{ $slot->schoolClass->name ?? 'Class' }}
                        </div>
                        <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.15rem;">
                            Room: {{ $slot->room_name ?? 'Main Class' }} · {{ ucfirst($slot->timetable_type) }}
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <span class="pill pill-info" style="font-size:0.7rem;">{{ $slot->day_name }}</span>
                        <div style="font-size:0.75rem; font-weight:600; margin-top:0.2rem; color:var(--primary-color);">
                            {{ $slot->start_time->format('H:i') }} - {{ $slot->end_time->format('H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:var(--text-secondary); padding:2rem;">
                    No timetable slots scheduled yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent Assessments & Grading -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">Recent Assessments</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Quick score entry and evaluation</div>
            </div>
            <a href="{{ route('academics.teacher-subjects') }}" class="btn btn-secondary btn-sm">
                <span>View All</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div style="display:flex; flex-direction:column; gap:0.75rem;">
            @forelse($recent_assessments as $ass)
                <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border-color); border-radius:10px; padding:0.85rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
                    <div>
                        <div style="font-weight:700; font-size:0.9rem;">{{ $ass->name }}</div>
                        <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.15rem;">
                            {{ $ass->teacherSubject->subject->name ?? 'Subject' }} ({{ $ass->teacherSubject->schoolClass->name ?? 'Class' }}) · {{ $ass->term->name ?? 'Term' }}
                        </div>
                    </div>
                    <div class="btn-group" style="align-items:center;">
                        <span style="font-size:0.75rem; color:var(--text-secondary); margin-right:0.5rem;">Max: {{ $ass->max_marks }} pts ({{ $ass->weight }}%)</span>
                        <a href="{{ route('academics.marks', $ass->id) }}" class="btn btn-primary btn-sm">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            <span>Enter Marks</span>
                        </a>
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:var(--text-secondary); padding:2rem;">
                    No assessments created yet.
                </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── NOTICES & DISCIPLINE RECORDS ─────────────────────────── --}}
<div class="dashboard-row" style="grid-template-columns:1fr 1fr;">

    <!-- Active School Announcements -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">Faculty Notices</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Official staff announcements</div>
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
                        {{ $ann->author->first_name ?? 'Admin' }} · {{ $ann->created_at->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:var(--text-secondary); padding:1.5rem;">
                    No notices posted.
                </div>
            @endforelse
        </div>
    </div>

    <!-- My Disciplinary Reports Logged -->
    <div class="glass-card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <div>
                <h3 style="font-size:1rem; font-weight:700;">My Incident Records</h3>
                <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.2rem;">Disciplinary files logged by you</div>
            </div>
            <a href="{{ route('discipline.index') }}" class="btn btn-secondary btn-sm">
                <span>Discipline Log</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>

        <div style="display:flex; flex-direction:column; gap:0.75rem;">
            @forelse($recent_discipline as $inc)
                <div style="background:rgba(255,255,255,0.02); border:1px solid var(--border-color); border-radius:10px; padding:0.85rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.35rem;">
                        <strong>{{ $inc->student->full_name ?? 'Student' }}</strong>
                        <span class="pill @if($inc->incident_type=='behavior') pill-danger @elseif($inc->incident_type=='academic') pill-warning @else pill-info @endif" style="font-size:0.65rem;">
                            {{ ucfirst($inc->incident_type) }}
                        </span>
                    </div>
                    <p style="font-size:0.8rem; color:var(--text-secondary); line-height:1.4; margin-bottom:0.4rem;">
                        {{ Str::limit($inc->details, 75) }}
                    </p>
                    <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.72rem; color:var(--text-secondary);">
                        <span>Action: {{ Str::limit($inc->action_taken, 25) }}</span>
                        <span>{{ $inc->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            @empty
                <div style="text-align:center; color:var(--text-secondary); padding:1.5rem;">
                    You have not logged any student disciplinary files.
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection

@extends('layouts.app')
@section('title', 'Parent Dashboard')
@section('page_title', 'Parent & Guardian Portal')

@section('content')
<!-- Welcome Card -->
<div class="glass-card" style="margin-bottom:2rem; border-left: 4px solid var(--primary-color);">
    <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:0.35rem;">Welcome, {{ Auth::user()->first_name }}!</h3>
    <p style="color:var(--text-secondary); font-size:0.9rem;">You are viewing academic and financial summaries for your registered children below.</p>
</div>

<!-- Children Cards -->
@forelse($children as $student)
<div class="glass-card" style="margin-bottom:2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
        <div style="display:flex; align-items:center; gap:1rem;">
            <div style="width:52px; height:52px; background:var(--primary-gradient); border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; font-weight:800; color:#fff; box-shadow:var(--shadow-accent);">
                {{ strtoupper(substr($student->first_name,0,1).substr($student->last_name,0,1)) }}
            </div>
            <div>
                <div style="font-size:1.1rem; font-weight:700;">{{ $student->full_name }}</div>
                <div style="font-size:0.82rem; color:var(--text-secondary);">
                    {{ $student->schoolClass->name ?? 'N/A' }} · Adm# {{ $student->admission_number }}
                    <span class="pill {{ $student->classification == 'boarding' ? 'pill-info' : 'pill-success' }}" style="margin-left:0.5rem; font-size:0.68rem;">
                        {{ ucfirst($student->classification) }}
                    </span>
                </div>
            </div>
        </div>
        <a href="{{ route('parent.child', $student->id) }}" class="btn btn-secondary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>View Full Profile</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem;">
        <!-- Fee Balance -->
        <div style="background:rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.15); border-radius:14px; padding:1.25rem;">
            <div class="metric-title">Outstanding Balance</div>
            <div style="font-size:1.75rem; font-weight:800; color:{{ ($student->account->balance ?? 0) < 0 ? 'var(--danger-color)' : 'var(--success-color)' }};">
                MWK {{ number_format(abs($student->account->balance ?? 0), 2) }}
            </div>
            <div style="font-size:0.75rem; color:var(--text-secondary);">{{ ($student->account->balance ?? 0) < 0 ? 'Amount Owed' : 'Credit Balance' }}</div>
        </div>

        <!-- Results Summary -->
        <div style="background:rgba(99,102,241,0.05); border:1px solid rgba(99,102,241,0.15); border-radius:14px; padding:1.25rem;">
            <div class="metric-title">Assessment Results</div>
            <div style="font-size:1.75rem; font-weight:800;">{{ $student->results->count() }}</div>
            <div style="font-size:0.75rem; color:var(--text-secondary);">Submissions Recorded</div>
        </div>

        <!-- Boarding Info -->
        @if($student->classification == 'boarding' && $student->activeAllocation)
        <div style="background:rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.15); border-radius:14px; padding:1.25rem;">
            <div class="metric-title">Boarding Bed</div>
            <div style="font-size:1.1rem; font-weight:700;">{{ $student->activeAllocation->bed->bed_number ?? 'N/A' }}</div>
            <div style="font-size:0.75rem; color:var(--text-secondary);">{{ $student->activeAllocation->bed->room->name ?? '' }} · {{ $student->activeAllocation->bed->room->dormitory->name ?? '' }}</div>
        </div>
        @endif
    </div>

    <!-- Latest 3 Results -->
    @if($student->results->count())
    <div>
        <h4 style="font-size:0.85rem; font-weight:700; text-transform:uppercase; letter-spacing:0.8px; color:var(--text-secondary); margin-bottom:0.75rem;">Recent Assessment Marks</h4>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr><th>Subject</th><th>Assessment</th><th>Marks</th><th>Out Of</th><th>Term</th></tr>
                </thead>
                <tbody>
                    @foreach($student->results->take(5) as $res)
                    <tr>
                        <td>{{ $res->assessment->teacherSubject->subject->name ?? 'N/A' }}</td>
                        <td>{{ $res->assessment->name ?? '' }}</td>
                        <td><strong>{{ $res->marks_obtained }}</strong></td>
                        <td>{{ $res->assessment->max_marks ?? '' }}</td>
                        <td>{{ $res->assessment->term->name ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@empty
<div class="glass-card" style="text-align:center; padding:3rem;">
    <p style="color:var(--text-secondary);">No children are currently linked to your account. Please contact the school administration.</p>
</div>
@endforelse

<!-- School Announcements for Parents -->
@if($announcements->count())
<div class="glass-card">
    <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">📢 School Notices for Parents</h3>
    @foreach($announcements as $ann)
    <div style="border-bottom:1px solid var(--border-color); padding:1rem 0;">
        <div style="font-weight:600; margin-bottom:0.35rem;">{{ $ann->title }}</div>
        <p style="color:var(--text-secondary); font-size:0.88rem; line-height:1.6;">{{ $ann->content }}</p>
        <div style="font-size:0.73rem; color:var(--text-secondary); margin-top:0.25rem;">{{ $ann->created_at->diffForHumans() }}</div>
    </div>
    @endforeach
</div>
@endif
@endsection

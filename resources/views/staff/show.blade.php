@extends('layouts.app')
@section('title', ($staff->user->name ?? 'Staff') . ' - AIM-LIGHT High School')
@section('page_title', 'Staff Profile')
@section('content')
    @if(session('success'))
        <div style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:0.9rem 1.25rem;margin-bottom:1.5rem;color:#10b981;font-size:0.9rem;">{{ session('success') }}</div>
    @endif
    <div class="glass-card" style="margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="display:flex;align-items:center;gap:1.25rem;">
            <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr($staff->user->first_name ?? 'S', 0, 1)) }}
            </div>
            <div>
                <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:0.3rem;">{{ $staff->user->name ?? 'N/A' }}</h2>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
                    <code style="font-size:0.8rem;background:rgba(255,255,255,0.06);padding:0.15rem 0.5rem;border-radius:4px;">{{ $staff->staff_number }}</code>
                    <span class="pill pill-info">{{ $staff->position->name ?? 'N/A' }}</span>
                    <span class="pill {{ $staff->attendance_status == 'present' ? 'pill-success' : 'pill-warning' }}">{{ $staff->attendance_status }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            @if(Auth::user()->role->slug === 'admin')
                <a href="{{ route('staff.edit', $staff->id) }}" class="btn btn-primary" style="font-size:0.85rem;">Edit Record</a>
            @endif
            <a href="{{ route('staff.index') }}" class="btn btn-secondary" style="font-size:0.85rem;">&#8592; Back</a>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
        <div class="glass-card">
            <h4 style="font-size:1rem;font-weight:600;margin-bottom:1rem;border-bottom:1px solid var(--border-color);padding-bottom:0.75rem;">Staff Details</h4>
            <div style="display:flex;flex-direction:column;gap:0.65rem;font-size:0.9rem;">
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Email</span><span>{{ $staff->user->email ?? 'N/A' }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Phone</span><span>{{ $staff->user->phone ?? '-' }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Department</span><strong>{{ $staff->department->name ?? 'N/A' }}</strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Position</span><strong>{{ $staff->position->name ?? 'N/A' }}</strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Employment</span><span>{{ str_replace('_',' ',ucfirst($staff->employment_status)) }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Attendance</span><span class="pill {{ $staff->attendance_status == 'present' ? 'pill-success' : 'pill-warning' }}">{{ $staff->attendance_status }}</span></div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid var(--border-color);padding-top:0.65rem;margin-top:0.35rem;"><span style="color:var(--text-secondary);">Joined</span><span>{{ $staff->created_at->format('d M Y') }}</span></div>
            </div>
        </div>
        <div class="glass-card">
            <h4 style="font-size:1rem;font-weight:600;margin-bottom:1rem;border-bottom:1px solid var(--border-color);padding-bottom:0.75rem;">Teaching Assignments</h4>
            @if($staff->teacherSubjects->count())
                <div style="display:flex;flex-direction:column;gap:0.5rem;">
                    @foreach($staff->teacherSubjects as $ts)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;border-bottom:1px solid var(--border-color);">
                        <strong style="font-size:0.9rem;">{{ $ts->subject->name ?? 'N/A' }}</strong>
                        <span class="pill pill-info" style="font-size:0.75rem;">{{ $ts->schoolClass->name ?? 'N/A' }}</span>
                    </div>
                    @endforeach
                </div>
            @else
                <p style="color:var(--text-secondary);font-size:0.9rem;font-style:italic;">No teaching assignments found.</p>
            @endif
        </div>
    </div>
@endsection

@extends('layouts.app')
@section('title', 'Discipline Files')
@section('page_title', 'Student Discipline Records')

@section('content')
<style>
    /* ── Animated blobs ── */
    .disc-blob {
        position: fixed;
        border-radius: 50%;
        filter: blur(90px);
        opacity: 0.12;
        animation: discBlobMorph 18s ease-in-out infinite alternate;
        z-index: -1;
        pointer-events: none;
    }
    .disc-blob-1 { width: 38vw; height: 38vw; background: #ef4444; top: -8%;  right: -8%; }
    .disc-blob-2 { width: 30vw; height: 30vw; background: #f59e0b; bottom: -8%; left: -8%; animation-delay: 6s; }
    .disc-blob-3 { width: 22vw; height: 22vw; background: #6366f1; top: 45%; left: 50%; transform: translate(-50%,-50%); animation-delay: 12s; }
    @keyframes discBlobMorph {
        0%   { border-radius: 50% 50% 50% 50%; transform: translate(0,0) scale(1); }
        33%  { border-radius: 60% 40% 55% 45%; transform: translate(25px,-15px) scale(1.08); }
        66%  { border-radius: 40% 60% 45% 55%; transform: translate(-15px,25px) scale(0.93); }
        100% { border-radius: 50% 50% 50% 50%; transform: translate(0,0) scale(1); }
    }

    /* ── Main container ── */
    .disc-container {
        max-width: 1000px;
        width: 100%;
        margin: 0 auto;
    }

    /* ── Wizard-style card ── */
    .disc-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 1.75rem;
        box-shadow: var(--shadow-card);
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .disc-card:hover {
        border-color: var(--border-color-hover);
        box-shadow: var(--shadow-glow);
    }

    /* ── Card header ── */
    .disc-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        flex-wrap: wrap;
        gap: 1rem;
    }
    .disc-header-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .disc-icon-box {
        background: linear-gradient(135deg, #ef4444, #f59e0b);
        border-radius: 1rem;
        padding: 0.7rem;
        color: white;
        box-shadow: 0 8px 20px rgba(239,68,68,0.3);
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .disc-icon-box svg { width: 1.5rem; height: 1.5rem; }
    .disc-header-title { font-size: 1.25rem; font-weight: 800; color: var(--text-primary); line-height: 1.2; }
    .disc-header-sub   { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.15rem; }

    /* ── Stats strip ── */
    .disc-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px,1fr));
        gap: 1px;
        background: var(--border-color);
    }
    .disc-stat {
        background: var(--bg-card);
        padding: 1.1rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        transition: background 0.2s;
    }
    .disc-stat:hover { background: var(--bg-card-hover, var(--sidebar-glow)); }
    .disc-stat-icon {
        width: 28px; height: 28px;
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: 0.2rem;
    }
    .disc-stat-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-secondary); }
    .disc-stat-value { font-size: 1.65rem; font-weight: 800; color: var(--text-primary); line-height: 1; }

    /* ── Toolbar ── */
    .disc-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-card);
        flex-wrap: wrap;
        gap: 0.6rem;
    }
    .disc-filter-form {
        display: flex;
        gap: 0.45rem;
        align-items: center;
        flex-wrap: wrap;
    }
    .disc-icon-input {
        position: relative;
    }
    .disc-icon-input svg {
        position: absolute;
        left: 0.55rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        pointer-events: none;
    }
    .disc-icon-input .form-control {
        padding: 0.42rem 0.75rem 0.42rem 1.9rem;
        font-size: 0.8rem;
        border-radius: 0.6rem;
    }

    /* ── Table ── */
    .disc-table-wrap { overflow-x: auto; }
    .disc-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.78rem;
    }
    .disc-table th {
        padding: 0.48rem 1rem;
        font-size: 0.64rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-card);
        white-space: nowrap;
        text-align: left;
    }
    .disc-table td {
        padding: 0.55rem 1rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
        vertical-align: middle;
    }
    .disc-table tbody tr:last-child td { border-bottom: none; }
    .disc-table tbody tr:hover td { background: var(--sidebar-glow); }

    /* ── Pagination ── */
    .disc-pagination {
        padding: 0.85rem 1.5rem;
        border-top: 1px solid var(--border-color);
        background: var(--bg-card);
    }

    /* ── Log Incident form ── */
    .disc-form-header {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 1.25rem 2rem;
        border-bottom: 1px solid var(--border-color);
    }
    .disc-form-icon {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        border-radius: 0.85rem;
        padding: 0.6rem;
        color: white;
        box-shadow: 0 6px 16px rgba(239,68,68,0.28);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .disc-form-body { padding: 1.5rem 2rem; }
    .disc-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    .disc-form-full { grid-column: span 2; }

    /* Input with icon */
    .disc-iw { position: relative; }
    .disc-iw .form-control { padding-left: 2.25rem; }
    .disc-iw textarea.form-control { padding-top: 0.6rem; }
    .disc-iw-icon {
        position: absolute;
        left: 0.7rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        pointer-events: none;
        z-index: 1;
    }
    .disc-iw-icon.top { top: 0.85rem; transform: none; }

    @media (max-width: 640px) {
        .disc-form-grid { grid-template-columns: 1fr; }
        .disc-form-full { grid-column: span 1; }
        .disc-card-header, .disc-toolbar, .disc-form-header, .disc-form-body, .disc-pagination { padding-left: 1rem; padding-right: 1rem; }
        .disc-hero { padding: 1.5rem 1.25rem 1.25rem; }
        .disc-hero-title { font-size: 1.45rem; }
        .disc-hero-emblem { width: 50px; height: 50px; border-radius: 0.9rem; }
        .disc-hero-emblem svg { width: 1.5rem; height: 1.5rem; }
    }

    /* ══ HERO PAGE HEADER ══ */
    .disc-hero {
        position: relative;
        border-radius: 1.75rem;
        margin-bottom: 1.5rem;
        overflow: hidden;
        background: linear-gradient(135deg, #1c0a0a 0%, #2d1010 40%, #1a1230 100%);
        border: 1px solid rgba(239,68,68,0.28);
        box-shadow: 0 20px 60px rgba(239,68,68,0.12), 0 6px 24px rgba(0,0,0,0.45);
        padding: 2rem 2.5rem 1.75rem;
    }
    html.light-theme .disc-hero {
        background: linear-gradient(135deg, #fff5f5 0%, #fef2f2 50%, #f0f0ff 100%);
        border-color: rgba(239,68,68,0.18);
        box-shadow: 0 10px 40px rgba(239,68,68,0.08), 0 2px 10px rgba(0,0,0,0.05);
    }
    /* diagonal texture */
    .disc-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: repeating-linear-gradient(
            -55deg, transparent, transparent 38px,
            rgba(255,255,255,0.018) 38px, rgba(255,255,255,0.018) 39px
        );
        pointer-events: none;
    }
    html.light-theme .disc-hero::before {
        background: repeating-linear-gradient(
            -55deg, transparent, transparent 38px,
            rgba(0,0,0,0.02) 38px, rgba(0,0,0,0.02) 39px
        );
    }
    /* glow orb top-right */
    .disc-hero-orb {
        position: absolute;
        top: -80px; right: -80px;
        width: 340px; height: 340px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(239,68,68,0.22) 0%, transparent 68%);
        pointer-events: none;
    }
    /* second orb bottom-left */
    .disc-hero-orb2 {
        position: absolute;
        bottom: -60px; left: -40px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(245,158,11,0.14) 0%, transparent 68%);
        pointer-events: none;
    }

    /* breadcrumb */
    .disc-crumb {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.7rem;
        font-weight: 600;
        color: rgba(255,255,255,0.38);
        margin-bottom: 1.1rem;
        position: relative; z-index: 2;
        letter-spacing: 0.3px;
    }
    html.light-theme .disc-crumb { color: rgba(0,0,0,0.35); }
    .disc-crumb a { color: inherit; text-decoration: none; transition: color 0.15s; }
    .disc-crumb a:hover { color: rgba(255,255,255,0.75); }
    html.light-theme .disc-crumb a:hover { color: rgba(0,0,0,0.65); }

    /* main row: left text + right CTA */
    .disc-hero-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1.25rem;
        flex-wrap: wrap;
        position: relative; z-index: 2;
    }
    .disc-hero-left { display: flex; align-items: center; gap: 1.25rem; }

    /* pulsing emblem */
    .disc-hero-emblem {
        width: 62px; height: 62px;
        border-radius: 1.1rem;
        background: linear-gradient(135deg, #ef4444, #f59e0b);
        box-shadow: 0 10px 30px rgba(239,68,68,0.5), inset 0 1px 0 rgba(255,255,255,0.22);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        animation: heroEmblemPulse 3.5s ease-in-out infinite;
    }
    @keyframes heroEmblemPulse {
        0%, 100% { box-shadow: 0 10px 30px rgba(239,68,68,0.45), inset 0 1px 0 rgba(255,255,255,0.22); }
        50%       { box-shadow: 0 14px 44px rgba(239,68,68,0.68), inset 0 1px 0 rgba(255,255,255,0.22); }
    }
    .disc-hero-emblem svg { width: 1.9rem; height: 1.9rem; color: #fff; }

    .disc-hero-title {
        font-size: 1.85rem;
        font-weight: 900;
        color: #ffffff;
        letter-spacing: -0.5px;
        line-height: 1.15;
    }
    html.light-theme .disc-hero-title { color: #1a0a0a; }

    .disc-hero-sub {
        font-size: 0.82rem;
        color: rgba(255,255,255,0.48);
        margin-top: 0.25rem;
        font-weight: 500;
        max-width: 400px;
        line-height: 1.5;
    }
    html.light-theme .disc-hero-sub { color: rgba(0,0,0,0.48); }

    /* CTA button */
    .disc-hero-cta {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.68rem 1.35rem;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff !important;
        border-radius: 0.85rem;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        box-shadow: 0 6px 20px rgba(239,68,68,0.45);
        border: 1px solid rgba(255,255,255,0.18);
        transition: all 0.22s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .disc-hero-cta:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 12px 32px rgba(239,68,68,0.58);
        color: #fff !important;
    }
    .disc-hero-cta svg { flex-shrink: 0; }

    /* stat chips */
    .disc-hero-chips {
        display: flex;
        gap: 0.55rem;
        flex-wrap: wrap;
        margin-top: 1.4rem;
        position: relative; z-index: 2;
    }
    .disc-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.38rem 0.85rem;
        border-radius: 2rem;
        font-size: 0.73rem;
        font-weight: 700;
        border: 1px solid;
        backdrop-filter: blur(8px);
        white-space: nowrap;
        letter-spacing: 0.2px;
    }
    .disc-chip-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    .disc-chip-num { font-size: 0.85rem; font-weight: 900; }

    .disc-chip-total  { background: rgba(239,68,68,0.18);  border-color: rgba(239,68,68,0.38);  color: #fca5a5; }
    .disc-chip-behav  { background: rgba(239,68,68,0.12);  border-color: rgba(239,68,68,0.28);  color: #fca5a5; }
    .disc-chip-acad   { background: rgba(245,158,11,0.15); border-color: rgba(245,158,11,0.32); color: #fcd34d; }
    .disc-chip-attend { background: rgba(99,102,241,0.15); border-color: rgba(99,102,241,0.32); color: #a5b4fc; }
    .disc-chip-warn   { background: rgba(239,68,68,0.12);  border-color: rgba(239,68,68,0.28);  color: #fca5a5; }

    html.light-theme .disc-chip-total  { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.22); color: #dc2626; }
    html.light-theme .disc-chip-behav  { background: rgba(239,68,68,0.06); border-color: rgba(239,68,68,0.18); color: #dc2626; }
    html.light-theme .disc-chip-acad   { background: rgba(245,158,11,0.08); border-color: rgba(245,158,11,0.25); color: #b45309; }
    html.light-theme .disc-chip-attend { background: rgba(99,102,241,0.08); border-color: rgba(99,102,241,0.22); color: #4338ca; }
    html.light-theme .disc-chip-warn   { background: rgba(239,68,68,0.06); border-color: rgba(239,68,68,0.18); color: #dc2626; }

    /* divider between hero chips and records card label */
    .disc-card-label {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--text-secondary);
    }
    .disc-card-label-dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: linear-gradient(135deg, #ef4444, #f59e0b);
        flex-shrink: 0;
    }
</style>


{{-- Animated blobs --}}
<div class="disc-blob disc-blob-1"></div>
<div class="disc-blob disc-blob-2"></div>
<div class="disc-blob disc-blob-3"></div>

<div class="disc-container">

    {{-- ══════════ HERO HEADER (standalone) ══════════ --}}
    <div class="disc-hero">
        {{-- Glow orbs --}}
        <div class="disc-hero-orb"></div>
        <div class="disc-hero-orb2"></div>

        {{-- Breadcrumb --}}
        <div class="disc-crumb">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <span class="disc-crumb-sep">›</span>
            <span>Discipline Files</span>
        </div>

        {{-- Main row: emblem + titles | CTA button --}}
        <div class="disc-hero-row">
            <div class="disc-hero-left">
                <div class="disc-hero-emblem">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13" stroke-width="2.2"/>
                        <line x1="12" y1="17" x2="12.01" y2="17" stroke-width="2.5"/>
                    </svg>
                </div>
                <div>
                    <div class="disc-hero-title">Discipline Records</div>
                    <div class="disc-hero-sub">Track, log and manage all student disciplinary incidents across the school.</div>
                </div>
            </div>

            <a href="#log-incident" class="disc-hero-cta">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Log Incident
            </a>
        </div>

        {{-- Stat chips row --}}
        <div class="disc-hero-chips">
            <span class="disc-chip disc-chip-total">
                <span class="disc-chip-dot" style="background:#ef4444;"></span>
                <span class="disc-chip-num">{{ $totalIncidents }}</span>
                Total Incidents
            </span>
            <span class="disc-chip disc-chip-behav">
                <span class="disc-chip-dot" style="background:#ef4444;"></span>
                <span class="disc-chip-num">{{ $behaviorCount }}</span>
                Behaviour
            </span>
            <span class="disc-chip disc-chip-acad">
                <span class="disc-chip-dot" style="background:#f59e0b;"></span>
                <span class="disc-chip-num">{{ $academicCount }}</span>
                Academic
            </span>
            <span class="disc-chip disc-chip-attend">
                <span class="disc-chip-dot" style="background:#6366f1;"></span>
                <span class="disc-chip-num">{{ $attendanceCount }}</span>
                Attendance
            </span>
            <span class="disc-chip disc-chip-warn">
                <span class="disc-chip-dot" style="background:#ef4444;"></span>
                <span class="disc-chip-num">{{ $warningsTotal }}</span>
                Warnings Issued
            </span>
        </div>
    </div>{{-- /disc-hero --}}

    {{-- ══════════ RECORDS CARD ══════════ --}}
    <div class="disc-card">

        {{-- Compact card label --}}
        <div class="disc-card-label">
            <span class="disc-card-label-dot"></span>
            Incident Log
            <span style="margin-left:auto; font-weight:600; text-transform:none; letter-spacing:0; font-size:0.75rem; color:var(--text-secondary);">
                {{ $records->total() }} record{{ $records->total() != 1 ? 's' : '' }}
            </span>
        </div>

        {{-- Toolbar --}}
        <div class="disc-toolbar">
            <form method="GET" class="disc-filter-form">
                <div class="disc-icon-input">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    <select name="incident_type" class="form-control" style="width:140px;" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <option value="behavior"   {{ request('incident_type')=='behavior'   ? 'selected' : '' }}>Behaviour</option>
                        <option value="academic"   {{ request('incident_type')=='academic'   ? 'selected' : '' }}>Academic</option>
                        <option value="attendance" {{ request('incident_type')=='attendance' ? 'selected' : '' }}>Attendance</option>
                    </select>
                </div>
                <div class="disc-icon-input">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="student_search" class="form-control"
                        style="width:185px;"
                        placeholder="Student name or ID"
                        value="{{ request('student_search') }}" />
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    Filter
                </button>
                @if(request('incident_type') || request('student_search'))
                    <a href="{{ route('discipline.index') }}" class="btn btn-secondary btn-sm">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                        Clear
                    </a>
                @endif
            </form>
        </div>


        {{-- Table --}}
        <div class="disc-table-wrap">
            <table class="disc-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Type</th>
                        <th>Details</th>
                        <th>Action Taken</th>
                        <th style="text-align:center;">Warnings</th>
                        <th>Recorded By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                    <tr>
                        <td>
                            <span style="font-weight:700; white-space:nowrap;">{{ $rec->student->full_name ?? 'N/A' }}</span>
                            @if($rec->student->admission_number ?? false)
                                <br><span style="font-size:0.67rem; color:var(--text-secondary);">{{ $rec->student->admission_number }}</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">{{ $rec->student->schoolClass->name ?? '—' }}</td>
                        <td>
                            <span class="pill @if($rec->incident_type=='behavior') pill-danger @elseif($rec->incident_type=='academic') pill-warning @else pill-info @endif">
                                {{ ucfirst($rec->incident_type) }}
                            </span>
                        </td>
                        <td style="max-width:180px; color:var(--text-secondary); font-size:0.76rem;">{{ Str::limit($rec->details, 65) }}</td>
                        <td style="max-width:180px; color:var(--text-secondary); font-size:0.76rem;">{{ Str::limit($rec->action_taken, 65) }}</td>
                        <td style="text-align:center;">
                            @if($rec->warnings_issued > 0)
                                <span class="pill pill-danger">{{ $rec->warnings_issued }} ⚠</span>
                            @else
                                <span style="color:var(--text-secondary); font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap; font-size:0.76rem;">{{ $rec->recorder->user->full_name ?? 'N/A' }}</td>
                        <td style="white-space:nowrap; font-size:0.73rem; color:var(--text-secondary);">{{ $rec->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:3rem; color:var(--text-secondary);">
                            <svg width="38" height="38" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="display:block; margin:0 auto 0.65rem; opacity:0.35;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                            </svg>
                            No discipline records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="disc-pagination">
            {{ $records->appends(request()->query())->links() }}
        </div>

    </div>{{-- /records card --}}


    {{-- ══════════ LOG INCIDENT CARD ══════════ --}}
    <div class="disc-card" id="log-incident">

        <div class="disc-form-header">
            <div class="disc-form-icon">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div style="font-size:1rem; font-weight:800; color:var(--danger-color);">Log New Incident</div>
                <div style="font-size:0.77rem; color:var(--text-secondary);">Record a disciplinary event for a student</div>
            </div>
        </div>

        <div class="disc-form-body">
            <form action="{{ route('discipline.store') }}" method="POST">
                @csrf
                <div class="disc-form-grid">

                    {{-- Student --}}
                    <div class="form-group">
                        <label class="form-label">Student *</label>
                        <div class="disc-iw">
                            <div class="disc-iw-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <select name="student_id" class="form-control" required>
                                <option value="">Select student…</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->full_name }} ({{ $student->admission_number }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Incident Type --}}
                    <div class="form-group">
                        <label class="form-label">Incident Type *</label>
                        <div class="disc-iw">
                            <div class="disc-iw-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                                </svg>
                            </div>
                            <select name="incident_type" class="form-control" required>
                                <option value="behavior">Behaviour / Conduct</option>
                                <option value="academic">Academic Misconduct</option>
                                <option value="attendance">Attendance Violation</option>
                            </select>
                        </div>
                    </div>

                    {{-- Warnings --}}
                    <div class="form-group disc-form-full">
                        <label class="form-label">Formal Warnings Issued</label>
                        <div class="disc-iw" style="max-width:170px;">
                            <div class="disc-iw-icon">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                            </div>
                            <input type="number" name="warnings_issued" class="form-control" value="0" min="0" max="5">
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="form-group">
                        <label class="form-label">Incident Details *</label>
                        <div class="disc-iw">
                            <div class="disc-iw-icon top">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <textarea name="details" class="form-control" rows="4" required placeholder="Describe what happened…"></textarea>
                        </div>
                    </div>

                    {{-- Action Taken --}}
                    <div class="form-group">
                        <label class="form-label">Action Taken *</label>
                        <div class="disc-iw">
                            <div class="disc-iw-icon top">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="9 11 12 14 22 4"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                                </svg>
                            </div>
                            <textarea name="action_taken" class="form-control" rows="4" required placeholder="Describe the disciplinary action taken…"></textarea>
                        </div>
                    </div>

                </div>

                {{-- Submit --}}
                <div style="margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid var(--border-color); display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-danger" style="min-width:195px;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                        Submit Incident Record
                    </button>
                </div>

            </form>
        </div>

    </div>{{-- /log incident card --}}

</div>{{-- /disc-container --}}
@endsection


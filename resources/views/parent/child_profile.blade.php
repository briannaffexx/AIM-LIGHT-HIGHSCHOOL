@extends('layouts.app')

@section('title', $student->full_name . ' — Child Profile')
@section('page_title', 'Child Profile: ' . $student->full_name)

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem;">
    <a href="{{ route('parent.dashboard') }}" class="btn btn-secondary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        <span>Back to Parent Portal</span>
    </a>
    <button onclick="window.print()" class="btn btn-primary btn-sm">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        <span>Print Summary</span>
    </button>
</div>

<!-- Main Summary Card -->
<div class="glass-card" style="margin-bottom:1.5rem;">
    <div style="display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap;">
        <div style="width:64px; height:64px; background:var(--primary-gradient); border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:800; color:#fff; box-shadow:var(--shadow-accent);">
            {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
        </div>
        <div style="flex:1;">
            <h2 style="font-size:1.35rem; font-weight:700; color:var(--text-primary);">{{ $student->full_name }}</h2>
            <div style="display:flex; gap:0.75rem; flex-wrap:wrap; align-items:center; margin-top:0.35rem;">
                <code>{{ $student->admission_number }}</code>
                <span class="pill pill-info">{{ $student->schoolClass->name ?? 'Class N/A' }}</span>
                <span class="pill {{ $student->classification == 'boarding' ? 'pill-success' : 'pill-info' }}">
                    {{ ucfirst(str_replace('_', ' ', $student->classification)) }}
                </span>
                <span class="pill {{ $student->status == 'active' ? 'pill-success' : 'pill-warning' }}">
                    {{ ucfirst($student->status) }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="dashboard-row" style="margin-bottom:1.5rem;">
    <div class="metric-card" style="border-left: 4px solid var(--primary-color);">
        <div class="metric-title">Fee Balance</div>
        <div class="metric-value" style="color:{{ ($student->account->balance ?? 0) > 0 ? 'var(--danger-color)' : 'var(--success-color)' }};">
            MWK {{ number_format(abs($student->account->balance ?? 0), 2) }}
        </div>
        <div class="metric-sub">{{ ($student->account->balance ?? 0) > 0 ? 'Outstanding Balance' : 'Fully Cleared / Credit' }}</div>
    </div>

    <div class="metric-card" style="border-left: 4px solid #10b981;">
        <div class="metric-title">Academic Results</div>
        <div class="metric-value">{{ $student->results->count() }}</div>
        <div class="metric-sub">Assessments Recorded</div>
    </div>

    <div class="metric-card" style="border-left: 4px solid #f59e0b;">
        <div class="metric-title">Library Borrows</div>
        <div class="metric-value">{{ $student->libraryBorrows->count() }}</div>
        <div class="metric-sub">Total Books Checked Out</div>
    </div>

    <div class="metric-card" style="border-left: 4px solid #ef4444;">
        <div class="metric-title">Discipline Files</div>
        <div class="metric-value">{{ $student->disciplineRecords->count() }}</div>
        <div class="metric-sub">Recorded Incidents</div>
    </div>
</div>

<!-- Academic Results & Report Card -->
<div class="glass-card" style="margin-bottom:1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.5rem;">
        <h3 style="font-size:1.1rem; font-weight:700;">Academic Performance & Marks</h3>
    </div>
    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Assessment</th>
                    <th>Term</th>
                    <th style="text-align:right;">Marks Obtained</th>
                    <th style="text-align:right;">Max Score</th>
                    <th style="text-align:right;">Percentage</th>
                </tr>
            </thead>
            <tbody>
                @forelse($student->results as $res)
                    @php
                        $maxMarks = $res->assessment->max_marks ?? 100;
                        $pct = $maxMarks > 0 ? round(($res->marks_obtained / $maxMarks) * 100, 1) : 0;
                    @endphp
                    <tr>
                        <td><strong>{{ $res->assessment->teacherSubject->subject->name ?? 'N/A' }}</strong></td>
                        <td>{{ $res->assessment->name ?? 'N/A' }}</td>
                        <td>{{ $res->assessment->term->name ?? '—' }}</td>
                        <td style="text-align:right; font-weight:700;">{{ $res->marks_obtained }}</td>
                        <td style="text-align:right; color:var(--text-secondary);">{{ $maxMarks }}</td>
                        <td style="text-align:right;">
                            <span class="pill {{ $pct >= 50 ? 'pill-success' : 'pill-danger' }}">{{ $pct }}%</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--text-secondary); padding:1.5rem;">No academic records published yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Library & Discipline Logs in 2 columns -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
    <!-- Library Borrows -->
    <div class="glass-card">
        <h4 style="font-size:1rem; font-weight:700; margin-bottom:1rem; border-bottom:1px solid var(--border-color); padding-bottom:0.6rem;">
            📚 Library Borrows
        </h4>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr><th>Book Title</th><th>Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($student->libraryBorrows as $b)
                        <tr>
                            <td><strong>{{ $b->book->title ?? 'N/A' }}</strong></td>
                            <td style="font-size:0.8rem;">{{ $b->borrowed_at ? $b->borrowed_at->format('d M Y') : '—' }}</td>
                            <td>
                                @if($b->returned_at)
                                    <span class="pill pill-success">Returned</span>
                                @else
                                    <span class="pill pill-info">Active</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center; color:var(--text-secondary); padding:1rem;">No books borrowed.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Discipline Records -->
    <div class="glass-card">
        <h4 style="font-size:1rem; font-weight:700; margin-bottom:1rem; border-bottom:1px solid var(--border-color); padding-bottom:0.6rem;">
            ⚠️ Discipline Records
        </h4>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr><th>Incident</th><th>Action</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($student->disciplineRecords as $d)
                        <tr>
                            <td>
                                <span class="pill pill-danger" style="font-size:0.68rem;">{{ ucfirst($d->incident_type) }}</span><br>
                                <span style="font-size:0.8rem;">{{ Str::limit($d->details, 40) }}</span>
                            </td>
                            <td style="font-size:0.8rem;">{{ Str::limit($d->action_taken, 40) }}</td>
                            <td style="font-size:0.78rem; white-space:nowrap;">{{ $d->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center; color:var(--text-secondary); padding:1rem;">Clean record — no incidents logged.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

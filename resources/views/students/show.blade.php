@extends('layouts.app')

@section('title', $student->full_name . ' - AIM-LIGHT High School')
@section('page_title', 'Student Profile')

@section('content')
    @if(session('success'))
        <div style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);border-radius:8px;padding:0.9rem 1.25rem;margin-bottom:1.5rem;color:#10b981;font-size:0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="glass-card" style="margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div style="display:flex;align-items:center;gap:1.25rem;">
            <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--primary-color),#6366f1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:#fff;flex-shrink:0;">
                {{ strtoupper(substr($student->first_name, 0, 1)) }}
            </div>
            <div>
                <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:0.3rem;">{{ $student->full_name }}</h2>
                <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
                    <code style="font-size:0.8rem;background:rgba(255,255,255,0.06);padding:0.15rem 0.5rem;border-radius:4px;">{{ $student->admission_number }}</code>
                    <span class="pill {{ $student->classification == 'boarding' ? 'pill-success' : 'pill-info' }}">{{ str_replace('_',' ',ucfirst($student->classification)) }}</span>
                    <span class="pill {{ $student->status == 'active' ? 'pill-success' : 'pill-warning' }}">{{ ucfirst($student->status) }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            @if(in_array(Auth::user()->role->slug, ['admin','head-teacher']))
                <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary" style="font-size:0.85rem;">Edit Record</a>
            @endif
            <a href="{{ route('academics.report-card', $student->id) }}" class="btn btn-secondary" style="font-size:0.85rem;">Report Card</a>
            @if(in_array(Auth::user()->role->slug, ['bursar','accountant','admin']))
                <a href="{{ route('finance.invoices', $student->id) }}" class="btn btn-secondary" style="font-size:0.85rem;">Billing</a>
            @endif
            <a href="{{ route('students.index') }}" class="btn btn-secondary" style="font-size:0.85rem;">&#8592; Back</a>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
        <div class="glass-card">
            <h4 style="font-size:1rem;font-weight:600;margin-bottom:1rem;border-bottom:1px solid var(--border-color);padding-bottom:0.75rem;">Personal Information</h4>
            <div style="display:flex;flex-direction:column;gap:0.65rem;font-size:0.9rem;">
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Full Name</span><strong>{{ $student->full_name }}</strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Email</span><span>{{ $student->user->email ?? 'N/A' }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Class</span><strong>{{ $student->schoolClass->name ?? 'N/A' }}</strong></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Type</span><span>{{ str_replace('_',' ',ucfirst($student->classification)) }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Status</span><span>{{ ucfirst($student->status) }}</span></div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid var(--border-color);padding-top:0.65rem;margin-top:0.35rem;"><span style="color:var(--text-secondary);">Guardian</span><span>{{ $student->guardian_name ?? '-' }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Guardian Phone</span><span>{{ $student->guardian_phone ?? '-' }}</span></div>
                <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Guardian Email</span><span>{{ $student->guardian_email ?? '-' }}</span></div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid var(--border-color);padding-top:0.65rem;margin-top:0.35rem;"><span style="color:var(--text-secondary);">Enrolled</span><span>{{ $student->created_at->format('d M Y') }}</span></div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            <div class="glass-card">
                <h4 style="font-size:1rem;font-weight:600;margin-bottom:1rem;border-bottom:1px solid var(--border-color);padding-bottom:0.75rem;">Boarding Allocation</h4>
                @if($student->activeAllocation)
                    <div style="display:flex;flex-direction:column;gap:0.5rem;font-size:0.9rem;">
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Dormitory</span><strong>{{ $student->activeAllocation->bed->room->dormitory->name ?? 'N/A' }}</strong></div>
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Room</span><span>{{ $student->activeAllocation->bed->room->room_number ?? 'N/A' }}</span></div>
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Bed</span><span>{{ $student->activeAllocation->bed->bed_number ?? 'N/A' }}</span></div>
                        <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Since</span><span>{{ $student->activeAllocation->allocated_at ? \Carbon\Carbon::parse($student->activeAllocation->allocated_at)->format('d M Y') : '-' }}</span></div>
                    </div>
                @else
                    <p style="color:var(--text-secondary);font-size:0.9rem;font-style:italic;">No active bed allocation.</p>
                @endif
            </div>
            @if($student->account)
            <div class="glass-card">
                <h4 style="font-size:1rem;font-weight:600;margin-bottom:1rem;border-bottom:1px solid var(--border-color);padding-bottom:0.75rem;">Fee Account</h4>
                <div style="display:flex;flex-direction:column;gap:0.5rem;font-size:0.9rem;">
                    <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Total Invoiced</span><strong>KES {{ number_format($student->account->total_invoiced, 2) }}</strong></div>
                    <div style="display:flex;justify-content:space-between;"><span style="color:var(--text-secondary);">Total Paid</span><strong style="color:#10b981;">KES {{ number_format($student->account->total_paid, 2) }}</strong></div>
                    <div style="display:flex;justify-content:space-between;border-top:1px solid var(--border-color);padding-top:0.5rem;"><span style="color:var(--text-secondary);">Balance Due</span><strong style="color:{{ $student->account->balance > 0 ? 'var(--danger-color)' : '#10b981' }};">KES {{ number_format($student->account->balance, 2) }}</strong></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @if($results->count())
    <div class="glass-card" style="margin-top:1.5rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h4 style="font-size:1rem;font-weight:600;">Recent Academic Results</h4>
            <a href="{{ route('academics.report-card', $student->id) }}" style="font-size:0.82rem;color:var(--primary-color);">Full Report Card &rarr;</a>
        </div>
        <div class="table-container">
            <table class="custom-table">
                <thead><tr><th>Subject</th><th>Assessment</th><th style="text-align:right;">Score</th><th style="text-align:right;">Max</th><th style="text-align:right;">%</th></tr></thead>
                <tbody>
                    @foreach($results as $res)
                    @php $pct = ($res->assessment->max_marks ?? 0) > 0 ? round(($res->marks_obtained / $res->assessment->max_marks) * 100, 1) : 0; @endphp
                    <tr>
                        <td><strong>{{ $res->assessment->teacherSubject->subject->name ?? 'N/A' }}</strong></td>
                        <td>{{ $res->assessment->name ?? 'N/A' }}</td>
                        <td style="text-align:right;">{{ $res->marks_obtained }}</td>
                        <td style="text-align:right;">{{ $res->assessment->max_marks ?? '-' }}</td>
                        <td style="text-align:right;"><strong style="color:{{ $pct >= 50 ? '#10b981' : 'var(--danger-color)' }};">{{ $pct }}%</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($student->histories->count())
    <div class="glass-card" style="margin-top:1.5rem;">
        <h4 style="font-size:1rem;font-weight:600;margin-bottom:1rem;border-bottom:1px solid var(--border-color);padding-bottom:0.75rem;">Student History Log</h4>
        <div class="table-container">
            <table class="custom-table">
                <thead><tr><th>Action</th><th>Details</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($student->histories->sortByDesc('created_at') as $h)
                    <tr>
                        <td><span class="pill pill-info" style="font-size:0.75rem;">{{ $h->action }}</span></td>
                        <td>{{ $h->details }}</td>
                        <td>{{ \Carbon\Carbon::parse($h->created_at)->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection

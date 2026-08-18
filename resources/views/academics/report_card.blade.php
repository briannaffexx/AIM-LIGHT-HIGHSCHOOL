@extends('layouts.app')

@section('title', 'Report Card - Boarding School System')
@section('page_title', 'Student Academic Performance Report')

@section('content')
    <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
        <!-- Header Info -->
        <div style="text-align: center; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
            <div class="logo-icon" style="margin: 0 auto 0.75rem auto; width: 45px; height: 45px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/></svg>
            </div>
            <h2 style="font-size: 1.35rem; font-weight: 700;">OFFICIAL STUDENT ACADEMIC PROGRESS CARD</h2>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem;">
                Academic Year: {{ $activeYear->name ?? '2025/2026' }}
            </p>
        </div>

        <!-- Student Meta Data Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; margin-bottom: 2rem; font-size: 0.9rem;">
            <div>
                <div style="margin-bottom: 0.5rem;"><span style="color: var(--text-secondary);">Student Name:</span> <strong>{{ $student->full_name }}</strong></div>
                <div><span style="color: var(--text-secondary);">Admission Number:</span> <code>{{ $student->admission_number }}</code></div>
            </div>
            <div>
                <div style="margin-bottom: 0.5rem;"><span style="color: var(--text-secondary);">Current Class:</span> <strong>{{ $student->schoolClass->name ?? 'N/A' }}</strong></div>
                <div><span style="color: var(--text-secondary);">Classification:</span> <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $student->classification) }}</span></div>
            </div>
        </div>

        <!-- Performance Comparison Table -->
        <h3 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1rem; color: #FFFFFF; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2.5"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
            Term-by-Term Comparative Performance Analysis
        </h3>

        <div class="table-container">
            <table class="custom-table" style="border: 1px solid var(--border-color); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.02);">
                        <th style="padding-left: 1.25rem;">Subject</th>
                        @foreach($terms as $term)
                            <th style="text-align: center;">{{ $term->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $subject => $scores)
                        <tr>
                            <td style="padding-left: 1.25rem;"><strong>{{ $subject }}</strong></td>
                            @foreach($terms as $term)
                                <td style="text-align: center;">
                                    @php
                                        $scoreVal = $scores[$term->name] ?? '-';
                                    @endphp
                                    <span style="font-weight: 600; @if(str_contains($scoreVal, '%')) color: #FFFFFF; @else color: var(--text-secondary); @endif">
                                        {{ $scoreVal }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($terms) + 1 }}" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No subjects registered in this class.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="border-top: 1px solid var(--border-color); margin-top: 2.5rem; padding-top: 1.5rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: var(--text-secondary);">
            <div>Generated on: {{ now()->format('Y-m-d H:i') }}</div>
            <div style="text-align: right; font-style: italic;">Verified by the Office of the Deputy Principal</div>
        </div>
    </div>
@endsection

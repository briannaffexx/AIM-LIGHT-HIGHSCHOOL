@extends('layouts.app')

@section('title', 'Teacher Dashboard - Boarding School System')
@section('page_title', 'Teacher Dashboard')

@section('content')
    <div class="card-grid">
        <div class="glass-card">
            <div class="metric-title">Classes Assigned</div>
            <div class="metric-value">{{ $classes_count }}</div>
            <div class="metric-indicator text-secondary">Registered teaching groups</div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Subjects Assigned</div>
            <div class="metric-value">{{ $subjects_count }}</div>
            <div class="metric-indicator text-secondary">Assigned syllabus lines</div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Total School Students</div>
            <div class="metric-value">{{ $total_students }}</div>
            <div class="metric-indicator text-secondary">Registered active learners</div>
        </div>
    </div>

    <!-- Subjects List -->
    <div class="glass-card" style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">My Active Subjects & Classes</h3>
        
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Class</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($my_subjects as $ms)
                        <tr>
                            <td><code>{{ $ms->subject->code }}</code></td>
                            <td><strong>{{ $ms->subject->name }}</strong></td>
                            <td>{{ $ms->schoolClass->name }}</td>
                            <td>
                                <a href="{{ route('academics.assessments', $ms->id) }}" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.8rem;">
                                    Manage Assessments & Grades
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">You are not assigned to teach any subjects yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'My Subjects - Boarding School System')
@section('page_title', 'My Assigned Teaching Subjects')

@section('content')
    <div class="glass-card">
        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Classes & Syllabus Assignments</h3>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Class Assignment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $ms)
                        <tr>
                            <td><code>{{ $ms->subject->code }}</code></td>
                            <td><strong>{{ $ms->subject->name }}</strong></td>
                            <td>{{ $ms->schoolClass->name }}</td>
                            <td>
                                <a href="{{ route('academics.assessments', $ms->id) }}" class="btn btn-primary btn-icon-sm" title="Manage Assessments & Marks">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">You do not have any classes assigned to teach.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

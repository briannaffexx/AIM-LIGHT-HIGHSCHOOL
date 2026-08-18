@extends('layouts.app')

@section('title', 'Record Grades - Boarding School System')
@section('page_title')
    Record Grades: {{ $assessment->name }} (Max: {{ $assessment->max_marks }})
@endsection

@section('content')
    <div class="glass-card" style="max-width: 800px; margin: 0 auto;">
        <div style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 600;">Grade Book</h3>
                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem;">
                    Subject: {{ $assessment->teacherSubject->subject->name }} | Class: {{ $assessment->teacherSubject->schoolClass->name }} | Term: {{ $assessment->term->name }}
                </p>
            </div>
            <a href="{{ route('academics.assessments', $assessment->teacher_subject_id) }}" class="btn btn-secondary" style="padding: 0.45rem 1rem; font-size: 0.8rem;">Back</a>
        </div>

        <form action="{{ route('academics.marks.store', $assessment->id) }}" method="POST">
            @csrf

            <div class="table-container" style="margin-bottom: 2rem;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ADM</th>
                            <th>Student Name</th>
                            <th style="text-align: right; width: 200px;">Marks Obtained</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td><code>{{ $student->admission_number }}</code></td>
                                <td><strong>{{ $student->full_name }}</strong></td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                        <input type="number" step="0.01" min="0" max="{{ $assessment->max_marks }}" 
                                               name="marks[{{ $student->id }}]" 
                                               class="form-control" 
                                               style="width: 100px; text-align: right; padding: 0.45rem;" 
                                               placeholder="0.0" 
                                               value="{{ old('marks.' . $student->id, $results[$student->id] ?? '') }}">
                                        <span style="font-size: 0.85rem; color: var(--text-secondary);">/ {{ $assessment->max_marks }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-secondary);">No students enrolled in this class.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="{{ route('academics.assessments', $assessment->teacher_subject_id) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Grades</button>
            </div>
        </form>
    </div>
@endsection

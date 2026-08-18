@extends('layouts.app')

@section('title', 'Assessments - Boarding School System')
@section('page_title')
    Manage Assessments: {{ $teacherSubject->subject->name }} ({{ $teacherSubject->schoolClass->name }})
@endsection

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Assessments List -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Registered Assessments</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Assessment Name</th>
                            <th>Term</th>
                            <th>Max Marks</th>
                            <th>Weight</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assessments as $ass)
                            <tr>
                                <td><strong>{{ $ass->name }}</strong></td>
                                <td>{{ $ass->term->name }}</td>
                                <td>{{ $ass->max_marks }}</td>
                                <td>{{ $ass->weight }}%</td>
                                <td>
                                    <a href="{{ route('academics.marks', $ass->id) }}" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                                        Record Grades
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No assessments configured for this subject yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Assessment Form -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">New Assessment</h3>

            <form action="{{ route('academics.assessments.store', $teacherSubject->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Assessment Name *</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Mid-Term Exam" required>
                </div>

                <div class="form-group">
                    <label for="term_id" class="form-label">Term *</label>
                    <select name="term_id" id="term_id" class="form-control" required>
                        @foreach($terms as $t)
                            <option value="{{ $t->id }}" {{ $t->is_active ? 'selected' : '' }}>{{ $t->name }} ({{ $t->academicYear->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="max_marks" class="form-label">Max Score *</label>
                        <input type="number" step="0.01" name="max_marks" id="max_marks" class="form-control" placeholder="e.g. 100" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="weight" class="form-label">Weight % *</label>
                        <input type="number" step="0.01" name="weight" id="weight" class="form-control" placeholder="e.g. 50" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Assessment</button>
            </form>
        </div>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Assessments - Boarding School System')
@section('page_title')
    Manage Assessments: {{ $teacherSubject->subject->name }} ({{ $teacherSubject->schoolClass->name }})
@endsection

@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.75rem;">
        <div>
            <span style="font-size:0.8rem; color:var(--text-secondary);">
                Academics &nbsp;/&nbsp; {{ $teacherSubject->subject->name }} &nbsp;/&nbsp; {{ $teacherSubject->schoolClass->name }}
            </span>
        </div>
        <a href="{{ route('academics.teacher-subjects') }}" class="btn btn-secondary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <span>Back to Subjects</span>
        </a>
    </div>

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
                                    <a href="{{ route('academics.marks', $ass->id) }}" class="btn btn-primary btn-sm">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        <span>Record Grades</span>
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

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>Create Assessment</span>
                </button>
            </form>
        </div>
    </div>
@endsection

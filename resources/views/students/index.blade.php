@extends('layouts.app')

@section('title', 'Students - Boarding School System')
@section('page_title', 'Student Directory')

@section('content')
    <div class="glass-card" style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 600;">Active Enrollment List</h3>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                <span>Register New Student</span>
            </a>
        </div>

        <!-- Filter and Search Bar -->
        <form action="{{ route('students.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label for="search" class="form-label">Search Name or ADM</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or admission number..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                <label for="class_id" class="form-label">Class</label>
                <select name="class_id" id="class_id" class="form-control">
                    <option value="">All Classes</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 0; min-width: 150px;">
                <label for="classification" class="form-label">Classification</label>
                <select name="classification" id="classification" class="form-control">
                    <option value="">All Types</option>
                    <option value="day_scholar" {{ request('classification') == 'day_scholar' ? 'selected' : '' }}>Day Scholar</option>
                    <option value="boarding" {{ request('classification') == 'boarding' ? 'selected' : '' }}>Boarder</option>
                </select>
            </div>

            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'class_id', 'classification']))
                    <a href="{{ route('students.index') }}" class="btn btn-secondary" style="color: var(--danger-color); border-color: rgba(239, 68, 68, 0.25);">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <span>Clear</span>
                    </a>
                @endif
            </div>
        </form>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ADM NO</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Classification</th>
                        <th>Guardian Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td><code>{{ $student->admission_number }}</code></td>
                            <td><strong>{{ $student->full_name }}</strong></td>
                            <td>{{ $student->schoolClass->name ?? 'N/A' }}</td>
                            <td>
                                <span class="pill {{ $student->classification == 'boarding' ? 'pill-info' : 'pill-success' }}">
                                    {{ str_replace('_', ' ', $student->classification) }}
                                </span>
                            </td>
                            <td>{{ $student->guardian_phone ?? '-' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary btn-sm" title="View Profile">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span>View</span>
                                    </a>
                                    <a href="{{ route('academics.report-card', $student->id) }}" class="btn btn-secondary btn-sm" title="Academic Report Card">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                        <span>Report</span>
                                    </a>
                                    @if(in_array(Auth::user()->role->slug, ['admin','head-teacher']))
                                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-secondary btn-sm" style="color:var(--primary-color); border-color:rgba(99,102,241,0.3);" title="Edit Student">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            <span>Edit</span>
                                        </a>
                                    @endif
                                    @if(in_array(Auth::user()->role->slug, ['bursar','accountant','admin']))
                                        <a href="{{ route('finance.invoices', $student->id) }}" class="btn btn-secondary btn-sm" style="color:#a855f7; border-color:rgba(168,85,247,0.3);" title="Student Invoices & Fees">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                            <span>Fees</span>
                                        </a>
                                    @endif
                                    @if(Auth::user()->role->slug === 'admin')
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Delete {{ $student->full_name }}? This cannot be undone.')" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-secondary btn-sm" style="color:var(--danger-color); border-color:rgba(239,68,68,0.25);" title="Delete Student">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No students found matching the criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $students->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

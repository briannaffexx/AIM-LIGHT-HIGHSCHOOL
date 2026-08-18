@extends('layouts.app')

@section('title', 'Students - Boarding School System')
@section('page_title', 'Student Directory')

@section('content')
    <div class="glass-card" style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 600;">Active Enrollment List</h3>
            <a href="{{ route('students.create') }}" class="btn btn-primary">Register New Student</a>
        </div>

        <!-- Filter and Search Bar -->
        <form action="{{ route('students.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label for="search" class="form-label">Search Name or ADM</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
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

            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->anyFilled(['search', 'class_id', 'classification']))
                <a href="{{ route('students.index') }}" class="btn btn-secondary" style="color: var(--danger-color); border-color: rgba(239, 68, 68, 0.2);">Clear</a>
            @endif
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
                                <span class="pill {{ $student->classification == 'boarding' ? 'pill-success' : 'pill-info' }}">
                                    {{ str_replace('_', ' ', $student->classification) }}
                                </span>
                            </td>
                            <td>{{ $student->guardian_phone ?? '-' }}</td>
                            <td>
                                <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                                    <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary" style="padding:0.3rem 0.6rem;font-size:0.75rem;">View</a>
                                    <a href="{{ route('academics.report-card', $student->id) }}" class="btn btn-secondary" style="padding:0.3rem 0.6rem;font-size:0.75rem;">Report</a>
                                    @if(in_array(Auth::user()->role->slug, ['admin','head-teacher']))
                                        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-secondary" style="padding:0.3rem 0.6rem;font-size:0.75rem;color:#818cf8;border-color:rgba(99,102,241,0.3);">Edit</a>
                                    @endif
                                    @if(in_array(Auth::user()->role->slug, ['bursar','accountant','admin']))
                                        <a href="{{ route('finance.invoices', $student->id) }}" class="btn btn-secondary" style="padding:0.3rem 0.6rem;font-size:0.75rem;color:#8B5CF6;border-color:rgba(139,92,246,0.3);">Fees</a>
                                    @endif
                                    @if(Auth::user()->role->slug === 'admin')
                                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Delete {{ $student->full_name }}? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-secondary" style="padding:0.3rem 0.6rem;font-size:0.75rem;color:var(--danger-color);border-color:rgba(239,68,68,0.2);">Delete</button>
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

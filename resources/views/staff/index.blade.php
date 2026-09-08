@extends('layouts.app')

@section('title', 'Staff Directory - Boarding School System')
@section('page_title', 'Staff Directory')

@section('content')
    <div class="glass-card" style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 600;">Active Staff Records</h3>
            <a href="{{ route('staff.create') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                <span>Register New Staff</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <form action="{{ route('staff.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label for="search" class="form-label">Search Staff Name or Code</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search staff..." value="{{ request('search') }}">
            </div>
            
            <div class="form-group" style="margin-bottom: 0; min-width: 180px;">
                <label for="department_id" class="form-label">Department</label>
                <select name="department_id" id="department_id" class="form-control">
                    <option value="">All Departments</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-secondary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                    <span>Filter</span>
                </button>
                @if(request()->anyFilled(['search', 'department_id']))
                    <a href="{{ route('staff.index') }}" class="btn btn-secondary" style="color: var(--danger-color); border-color: rgba(239, 68, 68, 0.25);">
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
                        <th>STAFF NO</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Employment Status</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staff as $member)
                        <tr>
                            <td><code>{{ $member->staff_number }}</code></td>
                            <td><strong>{{ $member->user->first_name ?? '' }} {{ $member->user->last_name ?? '' }}</strong><br><span style="font-size:0.75rem;color:var(--text-secondary);">{{ $member->user->email ?? '' }}</span></td>
                            <td>{{ $member->position->name ?? 'N/A' }}</td>
                            <td>{{ $member->department->name ?? 'N/A' }}</td>
                            <td><span class="pill pill-success">{{ ucfirst($member->employment_status) }}</span></td>
                            <td><span class="pill {{ $member->attendance_status == 'present' ? 'pill-success' : 'pill-danger' }}">{{ ucfirst($member->attendance_status) }}</span></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('staff.show', $member->id) }}" class="btn btn-secondary btn-sm" title="View Staff Profile">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                        <span>View</span>
                                    </a>
                                    @if(Auth::user()->role->slug === 'admin')
                                        <a href="{{ route('staff.edit', $member->id) }}" class="btn btn-secondary btn-sm" style="color:var(--primary-color); border-color:rgba(99,102,241,0.3);" title="Edit Staff Record">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            <span>Edit</span>
                                        </a>
                                        <form action="{{ route('staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Delete this staff record?')" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-secondary btn-sm" style="color:var(--danger-color); border-color:rgba(239,68,68,0.25);" title="Delete Staff">
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
                            <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No staff records found matching the criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $staff->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

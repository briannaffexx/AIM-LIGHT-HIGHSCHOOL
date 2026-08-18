@extends('layouts.app')

@section('title', 'Staff Directory - Boarding School System')
@section('page_title', 'Staff Directory')

@section('content')
    <div class="glass-card" style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 600;">Active Staff Records</h3>
            <a href="{{ route('staff.create') }}" class="btn btn-primary">Register New Staff</a>
        </div>

        <!-- Filter Bar -->
        <form action="{{ route('staff.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 200px;">
                <label for="search" class="form-label">Search Staff Name or Code</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
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

            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->anyFilled(['search', 'department_id']))
                <a href="{{ route('staff.index') }}" class="btn btn-secondary" style="color: var(--danger-color); border-color: rgba(239, 68, 68, 0.2);">Clear</a>
            @endif
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
                            <td><strong>{{ $member->user->name ?? 'N/A' }}</strong><br><span style="font-size:0.75rem;color:var(--text-secondary);">{{ $member->user->email ?? '' }}</span></td>
                            <td>{{ $member->position->name ?? 'N/A' }}</td>
                            <td>{{ $member->department->name ?? 'N/A' }}</td>
                            <td><span class="pill pill-success">{{ $member->employment_status }}</span></td>
                            <td><span class="pill {{ $member->attendance_status == 'present' ? 'pill-success' : 'pill-danger' }}">{{ $member->attendance_status }}</span></td>
                            <td>
                                <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                                    <a href="{{ route('staff.show', $member->id) }}" class="btn btn-secondary" style="padding:0.3rem 0.6rem;font-size:0.75rem;">View</a>
                                    @if(Auth::user()->role->slug === 'admin')
                                        <a href="{{ route('staff.edit', $member->id) }}" class="btn btn-secondary" style="padding:0.3rem 0.6rem;font-size:0.75rem;color:#818cf8;border-color:rgba(99,102,241,0.3);">Edit</a>
                                        <form action="{{ route('staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Delete this staff record?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-secondary" style="padding:0.3rem 0.6rem;font-size:0.75rem;color:var(--danger-color);border-color:rgba(239,68,68,0.2);">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No staff records found matching the criteria.</td>
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

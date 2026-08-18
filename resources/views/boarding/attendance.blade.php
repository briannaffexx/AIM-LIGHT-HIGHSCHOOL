@extends('layouts.app')

@section('title', 'Roll Call - Boarding School System')
@section('page_title', 'Boarding Attendance / Roll Call')

@section('content')
    <div class="glass-card">
        <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h3 style="font-size: 1.15rem; font-weight: 600;">Daily Check-In Roster</h3>

            <!-- Configuration Filters -->
            <form action="{{ route('boarding.attendance') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="date" class="form-label">Roll Call Date</label>
                    <input type="date" name="date" id="date" class="form-control" style="padding: 0.5rem;" value="{{ $date }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="roll_call_type" class="form-label">Time</label>
                    <select name="roll_call_type" id="roll_call_type" class="form-control" style="padding: 0.5rem;">
                        <option value="morning" {{ $type == 'morning' ? 'selected' : '' }}>Morning (06:30 AM)</option>
                        <option value="evening" {{ $type == 'evening' ? 'selected' : '' }}>Evening (09:00 PM)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary" style="padding: 0.55rem 1rem;">Load</button>
            </form>
        </div>

        <form action="{{ route('boarding.store-attendance') }}" method="POST">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="roll_call_type" value="{{ $type }}">

            <div class="table-container" style="margin-bottom: 1.5rem;">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>ADM</th>
                            <th>Student Name</th>
                            <th>Dormitory / Room / Bed</th>
                            <th style="width: 250px; text-align: center;">Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $alloc = $student->allocations->first();
                                $status = $existingAttendance[$student->id] ?? 'present';
                            @endphp
                            <tr>
                                <td><code>{{ $student->admission_number }}</code></td>
                                <td><strong>{{ $student->full_name }}</strong></td>
                                <td>
                                    @if($alloc)
                                        <span style="font-size: 0.85rem; color: var(--text-secondary);">
                                            {{ $alloc->bed->room->dormitory->name }} - {{ $alloc->bed->room->name }} - {{ $alloc->bed->bed_number }}
                                        </span>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--warning-color);">No allocation</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.75rem; justify-content: center; align-items: center;">
                                        <label style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.85rem; cursor: pointer; color: var(--success-color);">
                                            <input type="radio" name="status[{{ $student->id }}]" value="present" {{ $status == 'present' ? 'checked' : '' }}> P
                                        </label>
                                        <label style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.85rem; cursor: pointer; color: var(--danger-color);">
                                            <input type="radio" name="status[{{ $student->id }}]" value="absent" {{ $status == 'absent' ? 'checked' : '' }}> A
                                        </label>
                                        <label style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.85rem; cursor: pointer; color: var(--warning-color);">
                                            <input type="radio" name="status[{{ $student->id }}]" value="excused" {{ $status == 'excused' ? 'checked' : '' }}> E
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="remarks[{{ $student->id }}]" class="form-control" style="padding: 0.35rem 0.75rem; font-size: 0.85rem;" placeholder="e.g. sick bay, left early" value="{{ old('remarks.' . $student->id) }}">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 2rem;">No boarding students allocated to beds yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->isNotEmpty())
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary">Submit Roll Call</button>
                </div>
            @endif
        </form>
    </div>
@endsection

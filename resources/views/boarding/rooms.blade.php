@extends('layouts.app')

@section('title', 'Accommodation - Boarding School System')
@section('page_title', 'Boarding Accommodation Manager')

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Accommodation Layout -->
        <div>
            @forelse($houses as $house)
                <div class="glass-card" style="margin-bottom: 2rem;">
                    <h3 class="section-title" style="font-size: 1.25rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; color: var(--text-primary);">
                        {{ $house->name }} <span style="font-size: 0.85rem; font-weight: normal; color: var(--text-secondary);">- {{ $house->description }}</span>
                    </h3>

                    @forelse($house->dormitories as $dorm)
                        <div style="margin-bottom: 1.5rem; padding-left: 1rem;">
                            <h4 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1rem; color: var(--primary-color);">🏢 {{ $dorm->name }}</h4>
                            
                            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                                @foreach($dorm->rooms as $room)
                                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                            <strong style="font-size: 0.95rem; color: var(--text-primary);">Room: {{ $room->name }}</strong>
                                            <span style="font-size: 0.8rem; color: var(--text-secondary);">
                                                Capacity: {{ $room->beds->where('status', 'occupied')->count() }} / {{ $room->capacity }} Occupied
                                            </span>
                                        </div>

                                        <!-- Beds Grid -->
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem;">
                                            @foreach($room->beds as $bed)
                                                @php
                                                    $activeAlloc = $bed->allocations->where('vacated_at', null)->first();
                                                @endphp
                                                <div style="background: rgba(11, 15, 25, 0.4); border: 1px solid @if($bed->status == 'occupied') rgba(16, 185, 129, 0.2) @else var(--border-color) @endif; border-radius: 8px; padding: 0.75rem; font-size: 0.85rem;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                                        <span style="font-weight: 600;">🛏️ {{ $bed->bed_number }}</span>
                                                        <span class="pill @if($bed->status == 'occupied') pill-success @else pill-info @endif" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">
                                                             {{ $bed->status }}
                                                        </span>
                                                    </div>

                                                    @if($bed->status == 'occupied' && $activeAlloc)
                                                        <div style="color: var(--text-primary); font-weight: 500; margin-bottom: 0.5rem;">
                                                            {{ $activeAlloc->student->full_name }} <span style="font-size: 0.75rem; color: var(--text-secondary);">({{ $activeAlloc->student->schoolClass->name ?? 'N/A' }})</span>
                                                        </div>
                                                        <form action="{{ route('boarding.vacate', $activeAlloc->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; color: var(--danger-color); border-color: rgba(239, 68, 68, 0.25);">
                                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                                                <span>Vacate Bed</span>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span style="color: var(--text-secondary); display: block; margin-bottom: 0.5rem;">Vacant Bed</span>
                                                        <!-- Quick allocate trigger -->
                                                        <button onclick="document.getElementById('allocate_bed_id').value = '{{ $bed->id }}'; document.getElementById('student_id').focus();" class="btn btn-primary btn-sm" style="width: 100%;">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                                            <span>Allocate</span>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--text-secondary); padding-left: 1rem;">No dormitories configured under this house.</p>
                    @endforelse
                </div>
            @empty
                <div class="glass-card">
                    <p style="text-align: center; color: var(--text-secondary);">No boarding houses configured.</p>
                </div>
            @endforelse
        </div>

        <!-- Allocation Form Card -->
        <div class="glass-card" style="height: fit-content; position: sticky; top: 90px;">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Bed Allocation Panel</h3>

            <form action="{{ route('boarding.allocate') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="student_id" class="form-label">Select Boarding Student *</label>
                    <select name="student_id" id="student_id" class="form-control" required>
                        <option value="">Choose student...</option>
                        @foreach($unallocatedStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->admission_number }} - {{ $student->full_name }} ({{ $student->schoolClass->name ?? 'N/A' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="allocate_bed_id" class="form-label">Bed ID *</label>
                    <input type="text" name="bed_id" id="allocate_bed_id" class="form-control" placeholder="Select a vacant bed above..." readonly required>
                    <span style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 0.25rem; display: block;">Click "Allocate" on a vacant bed layout card above to select.</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    <span>Allocate Student</span>
                </button>
            </form>
        </div>
    </div>
@endsection

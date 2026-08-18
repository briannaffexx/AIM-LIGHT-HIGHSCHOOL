@extends('layouts.app')

@section('title', 'Accommodation - Boarding School System')
@section('page_title', 'Boarding Accommodation Manager')

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Accommodation Layout -->
        <div>
            @forelse($houses as $house)
                <div class="glass-card" style="margin-bottom: 2rem;">
                    <h3 class="section-title" style="font-size: 1.25rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem; color: #FFFFFF;">
                        {{ $house->name }} <span style="font-size: 0.85rem; font-weight: normal; color: var(--text-secondary);">- {{ $house->description }}</span>
                    </h3>

                    @forelse($house->dormitories as $dorm)
                        <div style="margin-bottom: 1.5rem; padding-left: 1rem;">
                            <h4 style="font-size: 1.05rem; font-weight: 600; margin-bottom: 1rem; color: var(--primary-color);">🏢 {{ $dorm->name }}</h4>
                            
                            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                                @foreach($dorm->rooms as $room)
                                    <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                            <strong style="font-size: 0.95rem; color: #FFFFFF;">Room: {{ $room->name }}</strong>
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
                                                        <div style="color: #FFFFFF; font-weight: 500; margin-bottom: 0.5rem;">
                                                            {{ $activeAlloc->student->full_name }} <span style="font-size: 0.75rem; color: var(--text-secondary);">({{ $activeAlloc->student->schoolClass->name ?? 'N/A' }})</span>
                                                        </div>
                                                        <form action="{{ route('boarding.vacate', $activeAlloc->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.7rem; width: 100%; color: var(--danger-color); border-color: rgba(239, 68, 68, 0.2);">
                                                                Vacate Bed
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span style="color: var(--text-secondary); display: block; margin-bottom: 0.5rem;">Vacant Bed</span>
                                                        <!-- Quick allocate trigger -->
                                                        <button onclick="document.getElementById('allocate_bed_id').value = '{{ $bed->id }}'; document.getElementById('student_id').focus();" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.7rem; width: 100%;">
                                                            Allocate
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

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Allocate Student</button>
            </form>
        </div>
    </div>
@endsection

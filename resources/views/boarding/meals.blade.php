@extends('layouts.app')

@section('title', 'Meals - Boarding School System')
@section('page_title', 'School Meal Planner & Menus')

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Weekly Menu Grid -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Weekly Menu Plan</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Breakfast</th>
                            <th>Lunch</th>
                            <th>Dinner</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $dayIndex => $dayName)
                            @php
                                $dayMeals = $schedules->where('day_of_week', $dayIndex);
                                $bf = $dayMeals->where('meal_type', 'breakfast')->first();
                                $lh = $dayMeals->where('meal_type', 'lunch')->first();
                                $dn = $dayMeals->where('meal_type', 'dinner')->first();
                            @endphp
                            <tr>
                                <td><strong>{{ $dayName }}</strong></td>
                                <td>
                                    @if($bf)
                                        <div style="font-size: 0.9rem; color: #FFFFFF; font-weight: 500;">{{ $bf->menu_item }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-secondary);">Time: {{ \Carbon\Carbon::parse($bf->time)->format('H:i') }}</div>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--text-secondary);">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lh)
                                        <div style="font-size: 0.9rem; color: #FFFFFF; font-weight: 500;">{{ $lh->menu_item }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-secondary);">Time: {{ \Carbon\Carbon::parse($lh->time)->format('H:i') }}</div>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--text-secondary);">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($dn)
                                        <div style="font-size: 0.9rem; color: #FFFFFF; font-weight: 500;">{{ $dn->menu_item }}</div>
                                        <div style="font-size: 0.75rem; color: var(--text-secondary);">Time: {{ \Carbon\Carbon::parse($dn->time)->format('H:i') }}</div>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--text-secondary);">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Meal Menu Form -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Configure Menu Item</h3>

            <form action="{{ route('boarding.meals.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="day_of_week" class="form-label">Day *</label>
                    <select name="day_of_week" id="day_of_week" class="form-control" required>
                        @foreach($days as $idx => $name)
                            <option value="{{ $idx }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="meal_type" class="form-label">Meal Category *</label>
                    <select name="meal_type" id="meal_type" class="form-control" required>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="menu_item" class="form-label">Menu Dish details *</label>
                    <input type="text" name="menu_item" id="menu_item" class="form-control" placeholder="e.g. Maize meal, Stewed beef, Kales" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="time" class="form-label">Serving Time *</label>
                    <input type="time" name="time" id="time" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Menu Item</button>
            </form>
        </div>
    </div>
@endsection

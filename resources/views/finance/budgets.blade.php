@extends('layouts.app')

@section('title', 'Budgets - Boarding School System')
@section('page_title', 'Term Budgets & Expenditure Ceilings')

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Budgets list -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Term Budgets</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Term / Academic Year</th>
                            <th style="text-align: right;">Budgeted</th>
                            <th style="text-align: right;">Actual Spent</th>
                            <th style="text-align: right;">Remaining</th>
                            <th style="text-align: center;">Utilization</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($budgets as $b)
                            @php
                                $rem = $b->budgeted_amount - $b->actual_spent;
                                $percent = $b->budgeted_amount > 0 ? round(($b->actual_spent / $b->budgeted_amount) * 100) : 0;
                            @endphp
                            <tr>
                                <td><strong style="text-transform: capitalize;">{{ str_replace('_', ' ', $b->category) }}</strong></td>
                                <td>{{ $b->term->name }} ({{ $b->term->academicYear->name }})</td>
                                <td style="text-align: right; font-weight: 600;">{{ number_format($b->budgeted_amount, 2) }}</td>
                                <td style="text-align: right; color: var(--danger-color);">{{ number_format($b->actual_spent, 2) }}</td>
                                <td style="text-align: right; @if($rem < 0) color: var(--danger-color); font-weight: 700; @else color: var(--success-color); @endif">
                                    {{ number_format($rem, 2) }}
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; justify-content: center;">
                                        <div style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); height: 8px; width: 60px; border-radius: 4px; overflow: hidden;">
                                            <div style="background: @if($percent > 100) var(--danger-color) @elseif($percent > 85) var(--warning-color) @else var(--primary-color) @endif; height: 100%; width: {{ min($percent, 100) }}%;"></div>
                                        </div>
                                        <span style="font-size: 0.8rem; font-weight: 600;">{{ $percent }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No budgets configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Budget Form -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Set Budget Limit</h3>

            <form action="{{ route('finance.budgets.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="term_id" class="form-label">Apply to Term *</label>
                    <select name="term_id" id="term_id" class="form-control" required>
                        @foreach($terms as $term)
                            <option value="{{ $term->id }}" {{ $term->is_active ? 'selected' : '' }}>{{ $term->name }} ({{ $term->academicYear->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="category" class="form-label">Expense Category *</label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="food">Food & Kitchen provisions</option>
                        <option value="utilities">Utilities (Water, Power, Net)</option>
                        <option value="maintenance">Facility Maintenance & Repair</option>
                        <option value="teaching_materials">Teaching Materials</option>
                        <option value="transport">School Transport & Fuel</option>
                        <option value="boarding_supplies">Boarding Dorm Supplies</option>
                        <option value="ict">ICT Infrastructure</option>
                        <option value="admin">Office & Administration</option>
                        <option value="sports">Sports & Co-curricular</option>
                        <option value="other">Other Operational Expense</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="budgeted_amount" class="form-label">Budget Limit Amount *</label>
                    <input type="number" step="0.01" name="budgeted_amount" id="budgeted_amount" class="form-control" placeholder="0.00" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Save Budget Ceiling</button>
            </form>
        </div>
    </div>
@endsection

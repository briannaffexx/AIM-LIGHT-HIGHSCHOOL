@extends('layouts.app')

@section('title', 'Expenditures - Boarding School System')
@section('page_title', 'Operational Expenditures & Alternative Income')

@section('content')
    <div class="dashboard-row">
        <!-- Expenses Ledger -->
        <div class="glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600;">Expenditures Ledger</h3>
                <!-- Trigger show create expense panel -->
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Recorded By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td><span class="pill pill-danger" style="text-transform: capitalize;">{{ str_replace('_', ' ', $exp->category) }}</span></td>
                                <td><strong>{{ number_format($exp->amount, 2) }}</strong></td>
                                <td><span style="font-size: 0.85rem;">{{ $exp->description }}</span></td>
                                <td>{{ $exp->recorder->name ?? 'N/A' }}</td>
                                <td>{{ $exp->date->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No expenditures recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1rem;">
                {{ $expenses->links() }}
            </div>
        </div>

        <!-- Expense Record Form -->
        <div class="glass-card" style="height: fit-content;">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--danger-color);">Log New Expense</h3>

            <form action="{{ route('finance.expenses.store') }}" method="POST">
                @csrf

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

                <div class="form-group">
                    <label for="amount" class="form-label">Amount Spent *</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" placeholder="0.00" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="description" class="form-label">Expense Description *</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Briefly explain the expenditure..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; background: var(--danger-color); border-color: rgba(239, 68, 68, 0.3);">Submit Expenditure</button>
            </form>
        </div>
    </div>

    <div class="dashboard-row" style="margin-top: 2rem;">
        <!-- Income Ledger -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Alternative Income Ledger</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Source</th>
                            <th>Description</th>
                            <th>Recorded By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($income as $inc)
                            <tr>
                                <td><span class="pill pill-success" style="text-transform: capitalize;">{{ $inc->category }}</span></td>
                                <td><strong>{{ number_format($inc->amount, 2) }}</strong></td>
                                <td>{{ $inc->source }}</td>
                                <td><span style="font-size: 0.85rem;">{{ $inc->description }}</span></td>
                                <td>{{ $inc->recorder->name ?? 'N/A' }}</td>
                                <td>{{ $inc->date->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No income transactions recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1rem;">
                {{ $income->links() }}
            </div>
        </div>

        <!-- Income Record Form -->
        <div class="glass-card" style="height: fit-content;">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--success-color);">Log Alternative Income</h3>

            <form action="{{ route('finance.income.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="income_category" class="form-label">Income Category *</label>
                    <select name="category" id="income_category" class="form-control" required>
                        <option value="donation">Donation / Endowment</option>
                        <option value="grant">Government Grant / Subsidy</option>
                        <option value="other">Other Income Stream</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="income_source" class="form-label">Funding Source *</label>
                    <input type="text" name="source" id="income_source" class="form-control" placeholder="e.g. Ministry of Education, Alumni" required>
                </div>

                <div class="form-group">
                    <label for="income_amount" class="form-label">Amount Received *</label>
                    <input type="number" step="0.01" name="amount" id="income_amount" class="form-control" placeholder="0.00" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="income_description" class="form-label">Details / Description</label>
                    <textarea name="description" id="income_description" class="form-control" rows="2" placeholder="Notes concerning this funding..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; background: var(--success-color); border-color: rgba(16, 185, 129, 0.3);">Submit Income</button>
            </form>
        </div>
    </div>
@endsection

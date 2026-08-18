@extends('layouts.app')

@section('title', 'Finance Overview - Boarding School System')
@section('page_title', 'Bursar & Accountant Panel')

@section('content')
    <div class="card-grid">
        <div class="glass-card">
            <div class="metric-title">Total Invoiced (Term 1)</div>
            <div class="metric-value">{{ number_format($total_invoiced, 2) }}</div>
            <div class="metric-indicator text-secondary">Student fee invoices issued</div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Fees Collected</div>
            <div class="metric-value">{{ number_format($total_collected, 2) }}</div>
            <div class="metric-indicator indicator-up">
                Collection Rate: {{ $collection_rate }}%
            </div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Total Operational Expenses</div>
            <div class="metric-value">{{ number_format($total_expenses, 2) }}</div>
            <div class="metric-indicator text-secondary">Current term expenditures</div>
        </div>

        <div class="glass-card">
            <div class="metric-title">Net Cash Position</div>
            <div class="metric-value {{ $net_position >= 0 ? '' : 'text-danger' }}">
                {{ number_format($net_position, 2) }}
            </div>
            <div class="metric-indicator text-secondary">Includes grants & donations</div>
        </div>
    </div>

    <div class="dashboard-row">
        <!-- Recent Fee Payments -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600;">Recent Fee Payments</h3>
                <a href="{{ route('finance.accounts') }}" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.8rem;">Student Accounts</a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>REF</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_payments as $pay)
                            <tr>
                                <td><code>{{ $pay->payment_reference }}</code></td>
                                <td>{{ $pay->invoice->student->full_name ?? 'N/A' }}</td>
                                <td><strong>{{ number_format($pay->amount, 2) }}</strong></td>
                                <td>{{ $pay->payment_date->format('Y-m-d') }}</td>
                                <td><span class="pill pill-success">{{ str_replace('_', ' ', $pay->payment_method) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary);">No payments recorded yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Budget Utilization Overview -->
        <div class="glass-card">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600;">Budget Utilization</h3>
                <a href="{{ route('finance.budgets') }}" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.8rem;">Manage Budgets</a>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Utilization</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($budgets as $b)
                            @php
                                $percent = $b->budgeted_amount > 0 ? round(($b->actual_spent / $b->budgeted_amount) * 100) : 0;
                            @endphp
                            <tr>
                                <td><strong style="text-transform: capitalize;">{{ str_replace('_', ' ', $b->category) }}</strong></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); height: 8px; width: 100px; border-radius: 4px; overflow: hidden;">
                                            <div style="background: @if($percent > 100) var(--danger-color) @elseif($percent > 85) var(--warning-color) @else var(--primary-color) @endif; height: 100%; width: {{ min($percent, 100) }}%;"></div>
                                        </div>
                                        <span style="font-size: 0.8rem; font-weight: 600;">{{ $percent }}%</span>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">
                                        Spent: {{ number_format($b->actual_spent, 2) }} / Limit: {{ number_format($b->budgeted_amount, 2) }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: var(--text-secondary);">No budgets defined.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart Row -->
    <div class="dashboard-row" style="margin-top: 1.5rem;">
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Revenue vs Expenses Overview</h3>
            <canvas id="financeChart" height="220"></canvas>
        </div>
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Income Breakdown</h3>
            <div style="display: flex; align-items: center; justify-content: center; padding: 1rem;">
                <canvas id="incomeBreakdown" width="260" height="260"></canvas>
            </div>
            <div style="display: flex; gap: 1.5rem; justify-content: center; margin-top: 1rem; font-size: 0.85rem; flex-wrap: wrap;">
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#6366f1;margin-right:5px;"></span>Fee Collections</span>
                <span><span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#22d3ee;margin-right:5px;"></span>Other Income</span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const finCtx = document.getElementById('financeChart');
if (finCtx) {
    new Chart(finCtx, {
        type: 'bar',
        data: {
            labels: ['Fee Collections', 'Other Income', 'Expenses', 'Net Position'],
            datasets: [{
                label: 'KES',
                data: [{{ $total_collected }}, {{ $total_other_income }}, {{ $total_expenses }}, {{ $net_position }}],
                backgroundColor: ['rgba(16,185,129,0.7)', 'rgba(34,211,238,0.7)', 'rgba(239,68,68,0.7)', '{{ $net_position >= 0 ? "rgba(99,102,241,0.7)" : "rgba(239,68,68,0.7)" }}'],
                borderColor: ['#10b981', '#22d3ee', '#ef4444', '{{ $net_position >= 0 ? "#6366f1" : "#ef4444" }}'],
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.6)' } },
                y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.6)', callback: v => 'KES ' + v.toLocaleString() } }
            }
        }
    });
}
const incCtx = document.getElementById('incomeBreakdown');
if (incCtx) {
    new Chart(incCtx, {
        type: 'doughnut',
        data: {
            labels: ['Fee Collections', 'Other Income'],
            datasets: [{ data: [{{ $total_collected }}, {{ $total_other_income }}], backgroundColor: ['#6366f1','#22d3ee'], borderColor: 'transparent', hoverOffset: 8 }]
        },
        options: { cutout: '65%', plugins: { legend: { display: false } } }
    });
}
</script>
@endpush

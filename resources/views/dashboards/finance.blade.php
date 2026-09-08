@extends('layouts.app')

@section('title', 'Finance Overview - Boarding School System')
@section('page_title', 'Bursar & Accountant Panel')

@section('content')
    <div class="dashboard-row" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-bottom:1.75rem;">
        <div class="metric-card" style="border-left:4px solid #6366f1;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div class="metric-title">Total Invoiced</div>
                <div style="width:38px;height:38px;background:rgba(99,102,241,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
            </div>
            <div class="metric-value" style="font-size:1.5rem;">{{ number_format($total_invoiced, 0) }}</div>
            <div class="metric-sub">MWK · Student fee invoices</div>
        </div>
        <div class="metric-card" style="border-left:4px solid #10b981;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div class="metric-title">Fees Collected</div>
                <div style="width:38px;height:38px;background:rgba(16,185,129,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>
            <div class="metric-value" style="font-size:1.5rem;color:var(--success-color);">{{ number_format($total_collected, 0) }}</div>
            <div class="metric-sub">Rate: {{ $collection_rate }}%</div>
        </div>
        <div class="metric-card" style="border-left:4px solid #ef4444;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div class="metric-title">Total Expenses</div>
                <div style="width:38px;height:38px;background:rgba(239,68,68,0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
            </div>
            <div class="metric-value" style="font-size:1.5rem;">{{ number_format($total_expenses, 0) }}</div>
            <div class="metric-sub">MWK · Operational expenditure</div>
        </div>
        <div class="metric-card" style="border-left:4px solid {{ $net_position >= 0 ? '#10b981' : '#ef4444' }};">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
                <div class="metric-title">Net Cash Position</div>
                <div style="width:38px;height:38px;background:rgba({{ $net_position >= 0 ? '16,185,129' : '239,68,68' }},0.12);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $net_position >= 0 ? '#10b981' : '#ef4444' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
            </div>
            <div class="metric-value" style="font-size:1.5rem;color:{{ $net_position >= 0 ? 'var(--success-color)' : 'var(--danger-color)' }};">{{ number_format(abs($net_position), 0) }}</div>
            <div class="metric-sub">{{ $net_position >= 0 ? 'Surplus' : 'Deficit' }} · Incl. grants</div>
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
const isDark = !document.documentElement.classList.contains('light-theme');
const tickColor = isDark ? 'rgba(148,163,184,0.8)' : 'rgba(100,116,139,0.9)';
const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';

const finCtx = document.getElementById('financeChart');
if (finCtx) {
    new Chart(finCtx, {
        type: 'bar',
        data: {
            labels: ['Fee Collections', 'Other Income', 'Expenses', 'Net Position'],
            datasets: [{
                label: 'MWK',
                data: [{{ $total_collected }}, {{ $total_other_income }}, {{ $total_expenses }}, {{ $net_position }}],
                backgroundColor: ['rgba(16,185,129,0.75)', 'rgba(34,211,238,0.75)', 'rgba(239,68,68,0.75)', '{{ $net_position >= 0 ? "rgba(99,102,241,0.75)" : "rgba(239,68,68,0.75)" }}'],
                borderColor: ['#10b981', '#22d3ee', '#ef4444', '{{ $net_position >= 0 ? "#6366f1" : "#ef4444" }}'],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: tickColor, font: { weight: '600' } } },
                y: { grid: { color: gridColor }, ticks: { color: tickColor, callback: v => 'MWK ' + v.toLocaleString() } }
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
            datasets: [{ data: [{{ $total_collected }}, {{ $total_other_income }}], backgroundColor: ['#6366f1','#22d3ee'], borderColor: 'transparent', hoverOffset: 6, borderRadius: 4 }]
        },
        options: { cutout: '68%', plugins: { legend: { display: false } } }
    });
}
</script>
@endpush

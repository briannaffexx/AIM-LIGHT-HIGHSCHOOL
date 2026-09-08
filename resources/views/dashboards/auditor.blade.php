@extends('layouts.app')

@section('title', 'Audit Logs - Boarding School System')
@section('page_title', 'Internal Audit Control Panel')

@section('content')
    <div class="dashboard-row" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-bottom:1.75rem;">
        <div class="metric-card" style="border-left:4px solid #6366f1;">
            <div class="metric-title">Fee Invoiced</div>
            <div class="metric-value" style="font-size:1.5rem;">{{ number_format($total_invoiced, 0) }}</div>
            <div class="metric-sub">MWK · Total student bills</div>
        </div>
        <div class="metric-card" style="border-left:4px solid #10b981;">
            <div class="metric-title">Cash Collected</div>
            <div class="metric-value" style="font-size:1.5rem;color:var(--success-color);">{{ number_format($total_collected, 0) }}</div>
            <div class="metric-sub">MWK · Payment receipts</div>
        </div>
        <div class="metric-card" style="border-left:4px solid #ef4444;">
            <div class="metric-title">Expenses Logged</div>
            <div class="metric-value" style="font-size:1.5rem;">{{ number_format($total_expenses, 0) }}</div>
            <div class="metric-sub">MWK · Payout receipts</div>
        </div>
    </div>

    <!-- Payments Audit Trail -->
    <div class="glass-card" style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Fee Receipt Transactions (Audited)</h3>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Receipt REF</th>
                        <th>Student ADM</th>
                        <th>Amount Paid</th>
                        <th>Method</th>
                        <th>Timestamp</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments_log as $pay)
                        <tr>
                            <td><code>{{ $pay->payment_reference }}</code></td>
                            <td>{{ $pay->invoice->student->admission_number ?? 'N/A' }} ({{ $pay->invoice->student->full_name ?? 'N/A' }})</td>
                            <td><strong>{{ number_format($pay->amount, 2) }}</strong></td>
                            <td><span class="pill pill-success">{{ $pay->payment_method }}</span></td>
                            <td>{{ $pay->payment_date->format('Y-m-d H:i') }}</td>
                            <td>{{ $pay->recorder->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No payment records logged.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expenses Audit Trail -->
    <div class="glass-card">
        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">General Expenditures Ledger</h3>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Audited Author</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses_log as $exp)
                        <tr>
                            <td><span class="pill pill-danger" style="text-transform: capitalize;">{{ str_replace('_', ' ', $exp->category) }}</span></td>
                            <td><strong>{{ number_format($exp->amount, 2) }}</strong></td>
                            <td>{{ $exp->date->format('Y-m-d') }}</td>
                            <td>{{ $exp->description }}</td>
                            <td>{{ $exp->recorder->name ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No expenditure records logged.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

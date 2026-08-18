@extends('layouts.app')

@section('title', 'Fee Statement - Boarding School System')
@section('page_title')
    Fee Statement: {{ $student->full_name }} (ADM: {{ $student->admission_number }})
@endsection

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        
        <!-- Invoices List & Record Payment -->
        <div>
            <!-- Invoices -->
            <div class="glass-card" style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Invoice Records</h3>

                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Invoice Details</th>
                                <th>Term</th>
                                <th style="text-align: right;">Amount Due</th>
                                <th style="text-align: right;">Paid</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $inv)
                                @php
                                    $paid = $inv->payments()->sum('amount');
                                    $bal = $inv->amount_due - $paid;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $inv->description }}</strong><br>
                                        <small style="color: var(--text-secondary);">Issued: {{ $inv->created_at->format('Y-m-d') }}</small>
                                    </td>
                                    <td>{{ $inv->term->name }}</td>
                                    <td style="text-align: right; font-weight: 600;">{{ number_format($inv->amount_due, 2) }}</td>
                                    <td style="text-align: right; color: var(--success-color);">{{ number_format($paid, 2) }}</td>
                                    <td style="text-align: center;">
                                        <span class="pill @if($inv->status == 'paid') pill-success @elseif($inv->status == 'partially_paid') pill-warning @else pill-danger @endif">
                                            {{ str_replace('_', ' ', $inv->status) }}
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        @if($inv->status !== 'paid')
                                            <button onclick="document.getElementById('pay_invoice_id').value = '{{ $inv->id }}'; document.getElementById('pay_amount').value = '{{ $bal }}'; document.getElementById('pay_amount').focus();" class="btn btn-primary" style="padding: 0.3rem 0.60rem; font-size: 0.7rem;">
                                                Pay
                                            </button>
                                        @else
                                            <span style="font-size: 0.75rem; color: var(--success-color); font-weight: 600;">Paid</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No invoices generated.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payments List -->
            <div class="glass-card">
                <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Payment History</h3>
                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Receipt REF</th>
                                <th>Amount Paid</th>
                                <th>Method</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $paymentsExist = false;
                            @endphp
                            @foreach($invoices as $inv)
                                @foreach($inv->payments as $pay)
                                    @php $paymentsExist = true; @endphp
                                    <tr>
                                        <td><code>{{ $pay->payment_reference }}</code></td>
                                        <td><strong>{{ number_format($pay->amount, 2) }}</strong></td>
                                        <td><span class="pill pill-success">{{ str_replace('_', ' ', $pay->payment_method) }}</span></td>
                                        <td>{{ $pay->payment_date->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            @endforeach

                            @if(!$paymentsExist)
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No payments registered yet.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Forms Side Column -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Record Payment Form -->
            <div class="glass-card">
                <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--success-color);">Record Fee Payment</h3>

                <form action="{{ route('finance.payments.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="pay_invoice_id" class="form-label">Invoice Details *</label>
                        <select name="invoice_id" id="pay_invoice_id" class="form-control" required>
                            <option value="">Choose invoice...</option>
                            @foreach($invoices as $inv)
                                @if($inv->status !== 'paid')
                                    <option value="{{ $inv->id }}">{{ $inv->description }} (Bal: {{ number_format($inv->amount_due - $inv->payments()->sum('amount'), 2) }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pay_amount" class="form-label">Amount *</label>
                        <input type="number" step="0.01" name="amount" id="pay_amount" class="form-control" placeholder="0.00" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="payment_method" class="form-label">Payment Channel *</label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="bank_transfer">Bank Wire Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cash">Cash Payment</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; background: var(--success-color); border-color: rgba(16, 185, 129, 0.3);">Submit Payment</button>
                </form>
            </div>

            <!-- Generate Invoice Form -->
            <div class="glass-card">
                <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Generate Custom Invoice</h3>

                <form action="{{ route('finance.invoices.store', $student->id) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="term_id" class="form-label">Select Academic Term *</label>
                        <select name="term_id" id="term_id" class="form-control" required>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ $term->is_active ? 'selected' : '' }}>{{ $term->name }} ({{ $term->academicYear->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Description *</label>
                        <input type="text" name="description" id="description" class="form-control" placeholder="e.g. Invoicing for Term 1 tuition" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="amount_due" class="form-label">Amount Due *</label>
                        <input type="number" step="0.01" name="amount_due" id="amount_due" class="form-control" placeholder="0.00" required>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Create Invoice</button>
                </form>
            </div>

        </div>

    </div>
@endsection

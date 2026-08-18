@extends('layouts.app')

@section('title', 'Student Accounts - Boarding School System')
@section('page_title', 'Student Financial Ledgers')

@section('content')
    <div class="glass-card">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 600;">Student Accounts Balances</h3>
        </div>

        <!-- Filter and Search -->
        <form action="{{ route('finance.accounts') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.5rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 250px;">
                <label for="search" class="form-label">Search Student Name or ADM</label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request('search'))
                <a href="{{ route('finance.accounts') }}" class="btn btn-secondary" style="color: var(--danger-color); border-color: rgba(239, 68, 68, 0.2);">Clear</a>
            @endif
        </form>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ADM</th>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Classification</th>
                        <th style="text-align: right;">Total Billed</th>
                        <th style="text-align: right;">Total Paid</th>
                        <th style="text-align: right;">Outstanding Balance</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $acc)
                        <tr>
                            <td><code>{{ $acc->student->admission_number }}</code></td>
                            <td><strong>{{ $acc->student->full_name }}</strong></td>
                            <td>{{ $acc->student->schoolClass->name ?? 'N/A' }}</td>
                            <td>
                                <span class="pill {{ $acc->student->classification == 'boarding' ? 'pill-success' : 'pill-info' }}">
                                    {{ str_replace('_', ' ', $acc->student->classification) }}
                                </span>
                            </td>
                            <td style="text-align: right;">{{ number_format($acc->total_invoiced, 2) }}</td>
                            <td style="text-align: right; color: var(--success-color); font-weight: 500;">
                                {{ number_format($acc->total_paid, 2) }}
                            </td>
                            <td style="text-align: right; @if($acc->balance > 0) color: var(--danger-color); font-weight: 700; @else color: var(--success-color); @endif">
                                {{ number_format($acc->balance, 2) }}
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('finance.invoices', $acc->student_id) }}" class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                                    View Statements
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No student accounts initialized.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 1.5rem;">
            {{ $accounts->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

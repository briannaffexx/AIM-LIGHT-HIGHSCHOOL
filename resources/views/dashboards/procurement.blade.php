@extends('layouts.app')

@section('title', 'Procurement Overview - Boarding School System')
@section('page_title', 'Procurement & Inventory Officer Panel')

@section('content')
    <div class="dashboard-row" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));margin-bottom:1.75rem;">
        <div class="metric-card" style="border-left:4px solid #6366f1;">
            <div class="metric-title">Total Requests</div>
            <div class="metric-value">{{ $total_requests }}</div>
            <div class="metric-sub">Logged supply requests</div>
        </div>
        <div class="metric-card" style="border-left:4px solid {{ $pending_requests > 0 ? '#f59e0b' : '#10b981' }};">
            <div class="metric-title">Pending Authorizations</div>
            <div class="metric-value" style="color:{{ $pending_requests > 0 ? 'var(--warning-color)' : 'var(--success-color)' }};">{{ $pending_requests }}</div>
            <div class="metric-sub">Awaiting head teacher review</div>
        </div>
        <div class="metric-card" style="border-left:4px solid #10b981;">
            <div class="metric-title">Approved Purchases</div>
            <div class="metric-value" style="color:var(--success-color);">{{ $approved_requests }}</div>
            <div class="metric-sub">Ready to be ordered</div>
        </div>
        <div class="metric-card" style="border-left:4px solid #22d3ee;">
            <div class="metric-title">Active Orders (POs)</div>
            <div class="metric-value">{{ $ordered_requests }}</div>
            <div class="metric-sub">Awaiting delivery</div>
        </div>
    </div>

    <!-- Requests list -->
    <div class="glass-card">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.15rem; font-weight: 600;">Purchase Requests & Orders Log</h3>
            <a href="{{ route('finance.procurement') }}" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.8rem;">Create PO / Request</a>
        </div>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Qty</th>
                        <th>Est. Cost</th>
                        <th>Requested By</th>
                        <th>Status</th>
                        <th>Authorized By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_requests as $req)
                        <tr>
                            <td><strong>{{ $req->item_name }}</strong></td>
                            <td>{{ $req->quantity }}</td>
                            <td><strong>{{ number_format($req->estimated_cost, 2) }}</strong></td>
                            <td>{{ $req->requester->name ?? 'N/A' }}</td>
                            <td>
                                <span class="pill @if($req->status == 'pending') pill-warning @elseif($req->status == 'approved') pill-success @elseif($req->status == 'ordered') pill-info @else pill-danger @endif">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td>{{ $req->approver->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No purchase requests logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

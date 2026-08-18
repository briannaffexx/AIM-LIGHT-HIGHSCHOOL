@extends('layouts.app')

@section('title', 'Procurement - Boarding School System')
@section('page_title', 'Procurement & Purchase Orders Control')

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Purchase Requests -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Purchase Requests Log</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th>Est. Cost</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
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
                                <td>
                                    @if($req->status === 'approved' && in_array(Auth::user()->role->slug, ['procurement-officer', 'admin']))
                                        <!-- Quick trigger PO form -->
                                        <button onclick="document.getElementById('po_req_id').value = '{{ $req->id }}'; document.getElementById('po_item_desc').innerText = '{{ $req->item_name }} (x{{ $req->quantity }})'; document.getElementById('po_total').value = '{{ $req->estimated_cost }}'; document.getElementById('supplier_id').focus();" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.7rem;">
                                            Generate PO
                                        </button>
                                    @elseif($req->status === 'pending' && in_array(Auth::user()->role->slug, ['head-teacher', 'admin']))
                                        <div style="display: flex; gap: 0.25rem;">
                                            <form action="{{ route('finance.procurement.request.approve', $req->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-primary" style="padding: 0.3rem 0.6rem; font-size: 0.7rem;">Approve</button>
                                            </form>
                                            <form action="{{ route('finance.procurement.request.reject', $req->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary" style="padding: 0.3rem 0.6rem; font-size: 0.7rem; color: var(--danger-color); border-color: rgba(239, 68, 68, 0.2);">Reject</button>
                                            </form>
                                        </div>
                                    @else
                                        <span style="font-size: 0.75rem; color: var(--text-secondary); font-style: italic;">Locked</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No purchase requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Purchase Request Form -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">New Purchase Request</h3>

            <form action="{{ route('finance.procurement.request.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="item_name" class="form-label">Item / Supply Name *</label>
                    <input type="text" name="item_name" id="item_name" class="form-control" placeholder="e.g. 50 Packs of Blue Pens" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="quantity" class="form-label">Quantity *</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" placeholder="1" required>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="estimated_cost" class="form-label">Estimated Cost *</label>
                        <input type="number" step="0.01" name="estimated_cost" id="estimated_cost" class="form-control" placeholder="0.00" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Submit Request</button>
            </form>
        </div>
    </div>

    <!-- Purchase Orders (POs) -->
    <div class="dashboard-row" style="margin-top: 2rem;">
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Active Purchase Orders (POs)</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>PO REF</th>
                            <th>Item Details</th>
                            <th>Supplier</th>
                            <th style="text-align: right;">Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td><code>{{ $order->order_number }}</code></td>
                                <td>{{ $order->purchaseRequest->item_name ?? 'N/A' }}</td>
                                <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                                <td style="text-align: right; font-weight: 600;">{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="pill @if($order->status == 'ordered') pill-warning @elseif($order->status == 'delivered') pill-info @else pill-success @endif">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($order->status !== 'paid')
                                        <form action="{{ route('finance.procurement.order.status', $order->id) }}" method="POST" style="display: flex; gap: 0.25rem;">
                                            @csrf
                                            <select name="status" class="form-control" style="padding: 0.25rem; font-size: 0.75rem; width: 100px;" onchange="this.form.submit()">
                                                <option value="ordered" {{ $order->status == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                                <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                                            </select>
                                        </form>
                                    @else
                                        <span style="font-size: 0.75rem; color: var(--success-color); font-weight: 600;">Settled</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No active purchase orders.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Purchase Order Form -->
        <div class="glass-card" style="height: fit-content;">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--info-color);">Generate Purchase Order</h3>

            <form action="{{ route('finance.procurement.order.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="po_req_id" class="form-label">Authorized Request ID *</label>
                    <input type="text" name="purchase_request_id" id="po_req_id" class="form-control" placeholder="Select an approved request..." readonly required>
                    <div id="po_item_desc" style="font-size: 0.75rem; color: var(--success-color); margin-top: 0.25rem; font-weight: 600;"></div>
                </div>

                <div class="form-group">
                    <label for="supplier_id" class="form-label">Supplier *</label>
                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                        <option value="">Select supplier...</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="po_total" class="form-label">Final Order Value *</label>
                    <input type="number" step="0.01" name="total_amount" id="po_total" class="form-control" placeholder="0.00" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; background: var(--info-color); border-color: rgba(59, 130, 246, 0.3);">Dispatch Purchase Order</button>
            </form>
        </div>
    </div>
@endsection

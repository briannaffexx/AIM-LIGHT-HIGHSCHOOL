@extends('layouts.app')
@section('title', 'Asset Inventory')
@section('page_title', 'School Assets & Inventory')

@section('content')
<div class="dashboard-row" style="grid-template-columns: 2fr 1fr; align-items: start;">
    <div class="glass-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.5rem;">
            <h3 style="font-size:1.1rem; font-weight:700;">Asset Register</h3>
            <form method="GET" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <select name="category" class="form-control" style="width:160px;" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category')==$cat?'selected':'' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
                <select name="status" class="form-control" style="width:160px;" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="good"            {{ request('status')=='good'            ?'selected':'' }}>Good</option>
                    <option value="damaged"         {{ request('status')=='damaged'         ?'selected':'' }}>Damaged</option>
                    <option value="need_replacement"{{ request('status')=='need_replacement'?'selected':'' }}>Needs Replacement</option>
                </select>
            </form>
        </div>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Asset Name</th>
                        <th>Category</th>
                        <th>Total Qty</th>
                        <th>Assigned</th>
                        <th>Available</th>
                        <th>Condition</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td><span class="pill pill-info" style="text-transform:capitalize;">{{ $item->category }}</span></td>
                        <td>{{ $item->total_quantity }}</td>
                        <td>{{ $item->assigned_quantity }}</td>
                        <td style="font-weight:700; color:{{ ($item->total_quantity - $item->assigned_quantity) > 0 ? 'var(--success-color)':'var(--danger-color)' }};">
                            {{ $item->total_quantity - $item->assigned_quantity }}
                        </td>
                        <td>
                            <span class="pill @if($item->status=='good') pill-success @elseif($item->status=='damaged') pill-danger @else pill-warning @endif">
                                {{ ucfirst(str_replace('_',' ',$item->status)) }}
                            </span>
                        </td>
                        <td style="font-size:0.8rem; color:var(--text-secondary);">{{ $item->condition_notes ?? '—' }}</td>
                        <td>
                            <form action="{{ route('inventory.update', $item->id) }}" method="POST" style="display:flex; gap:0.25rem; align-items:center;">
                                @csrf @method('PUT')
                                <select name="status" class="form-control" style="padding:0.2rem; font-size:0.75rem; width:120px;">
                                    <option value="good"            {{ $item->status=='good'?'selected':'' }}>Good</option>
                                    <option value="damaged"         {{ $item->status=='damaged'?'selected':'' }}>Damaged</option>
                                    <option value="need_replacement"{{ $item->status=='need_replacement'?'selected':'' }}>Need Repl.</option>
                                </select>
                                <button type="submit" class="btn btn-secondary btn-sm" style="padding:0.25rem 0.5rem; font-size:0.72rem;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                    <span>Update</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center; color:var(--text-secondary); padding:2rem;">No assets recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $items->links() }}</div>
    </div>

    <!-- Add Asset Form -->
    <div class="glass-card">
        <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">Register Asset</h3>
        <form action="{{ route('inventory.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Asset Name *</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Student Desks">
            </div>
            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-control" required>
                    <option value="furniture">Furniture</option>
                    <option value="lab">Lab Equipment</option>
                    <option value="computers">ICT & Computers</option>
                    <option value="sports">Sports Equipment</option>
                    <option value="boarding">Boarding Supplies</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:0.75rem;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Total Qty *</label>
                    <input type="number" name="total_quantity" class="form-control" min="1" value="1" required>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Assigned Qty</label>
                    <input type="number" name="assigned_quantity" class="form-control" min="0" value="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Condition *</label>
                <select name="status" class="form-control" required>
                    <option value="good">Good</option>
                    <option value="damaged">Damaged</option>
                    <option value="need_replacement">Needs Replacement</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Notes</label>
                <textarea name="condition_notes" class="form-control" rows="2" placeholder="Optional condition notes..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
                <span>Register Asset</span>
            </button>
        </form>
    </div>
</div>
@endsection

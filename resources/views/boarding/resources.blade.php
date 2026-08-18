@extends('layouts.app')

@section('title', 'Resources - Boarding School System')
@section('page_title', 'Boarding Resources Inventory')

@section('content')
    <div class="dashboard-row" style="grid-template-columns: 2fr 1fr;">
        <!-- Resources list -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Inventory Roster</h3>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Resource Name</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resources as $res)
                            <tr>
                                <td><strong>{{ $res->name }}</strong></td>
                                <td><span class="pill pill-info" style="text-transform: capitalize;">{{ $res->category }}</span></td>
                                <td>
                                    <span class="pill @if($res->status == 'good') pill-success @elseif($res->status == 'damaged') pill-danger @else pill-warning @endif" style="text-transform: capitalize;">
                                        {{ str_replace('_', ' ', $res->status) }}
                                    </span>
                                </td>
                                <td><span style="font-size: 0.85rem; color: var(--text-secondary);">{{ $res->notes ?? '-' }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 1.5rem;">No boarding resources registered yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 1rem;">
                {{ $resources->links() }}
            </div>
        </div>

        <!-- Add Resource Form -->
        <div class="glass-card">
            <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem;">Register Resource Item</h3>

            <form action="{{ route('boarding.resources.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Resource Item Name *</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="e.g. High-Density Mattress M-101" required>
                </div>

                <div class="form-group">
                    <label for="category" class="form-label">Category *</label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="bed">Bed Frame</option>
                        <option value="mattress">Mattress</option>
                        <option value="locker">Locker</option>
                        <option value="blanket">Blanket / Linen</option>
                        <option value="kitchen">Kitchen Equipment</option>
                        <option value="dining">Dining Equipment</option>
                        <option value="cleaning">Cleaning Supplies</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status" class="form-label">Item Condition *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="good">Good / Serviceable</option>
                        <option value="damaged">Damaged / Needs Repair</option>
                        <option value="need_replacement">Needs Outright Replacement</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="notes" class="form-label">Location / Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="e.g. Room 102, Blue House"></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Add to Inventory</button>
            </form>
        </div>
    </div>
@endsection

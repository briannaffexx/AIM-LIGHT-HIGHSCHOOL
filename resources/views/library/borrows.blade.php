@extends('layouts.app')
@section('title', 'Library - Borrowing')
@section('page_title', 'Library — Book Borrows & Returns')

@section('content')
<div class="dashboard-row" style="grid-template-columns: 2fr 1fr; align-items: start;">
    <!-- Active Borrows Table -->
    <div class="glass-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.5rem;">
            <h3 style="font-size:1.1rem; font-weight:700;">Borrow Transactions</h3>
            <div style="display:flex; align-items:center; gap:0.5rem;">
                <a href="{{ route('library.books') }}" class="btn btn-secondary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    <span>Book Catalogue</span>
                </a>
                <form method="GET" style="display:flex; gap:0.5rem;">
                    <select name="status" class="form-control" style="width:140px;" onchange="this.form.submit()">
                        <option value="">All Status</option>
                        <option value="borrowed" {{ request('status')=='borrowed'  ? 'selected':'' }}>Active</option>
                        <option value="returned" {{ request('status')=='returned'  ? 'selected':'' }}>Returned</option>
                        <option value="overdue"  {{ request('status')=='overdue'   ? 'selected':'' }}>Overdue</option>
                    </select>
                </form>
            </div>
        </div>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Student</th>
                        <th>Borrowed</th>
                        <th>Due Date</th>
                        <th>Returned</th>
                        <th>Fine</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrows as $borrow)
                    @php $overdue = is_null($borrow->returned_at) && $borrow->due_at && $borrow->due_at->isPast(); @endphp
                    <tr>
                        <td><strong>{{ $borrow->book->title ?? 'N/A' }}</strong></td>
                        <td>{{ $borrow->student->full_name ?? 'N/A' }}</td>
                        <td style="font-size:0.82rem;">{{ $borrow->borrowed_at->format('d M Y') }}</td>
                        <td style="font-size:0.82rem; @if($overdue) color:var(--danger-color); font-weight:700; @endif">{{ $borrow->due_at?->format('d M Y') ?? '—' }}</td>
                        <td style="font-size:0.82rem; color:var(--success-color);">{{ $borrow->returned_at?->format('d M Y') ?? '—' }}</td>
                        <td>@if($borrow->fine_amount > 0)<span class="pill pill-danger">MWK {{ number_format($borrow->fine_amount,2) }}</span>@else—@endif</td>
                        <td>
                            @if($borrow->returned_at)
                                <span class="pill pill-success">Returned</span>
                            @elseif($overdue)
                                <span class="pill pill-danger">Overdue</span>
                            @else
                                <span class="pill pill-info">Active</span>
                            @endif
                        </td>
                        <td>
                            @if(!$borrow->returned_at)
                            <form action="{{ route('library.borrows.return', $borrow->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-icon-sm" style="color:var(--success-color); border-color:rgba(16,185,129,0.3);" title="Mark as Returned">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center; color:var(--text-secondary); padding:2rem;">No borrow records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $borrows->links() }}</div>
    </div>

    <!-- Issue Book Form -->
    <div class="glass-card">
        <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1.5rem; color:var(--primary-color);">Issue a Book</h3>
        <form action="{{ route('library.borrows.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Select Book *</label>
                <select name="book_id" class="form-control" required>
                    <option value="">Choose available book...</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}">{{ $book->title }} ({{ $book->available_copies }} left)</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Student *</label>
                <select name="student_id" class="form-control" required>
                    <option value="">Select student...</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->full_name }} — {{ $student->schoolClass->name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Due Date *</label>
                <input type="date" name="due_at" class="form-control" required min="{{ now()->addDay()->format('Y-m-d') }}" value="{{ now()->addDays(14)->format('Y-m-d') }}">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/><line x1="12" y1="5" x2="12" y2="19"/></svg>
                <span>Issue Book</span>
            </button>
        </form>
    </div>
</div>
@endsection

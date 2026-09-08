@extends('layouts.app')
@section('title', 'Library - Books')
@section('page_title', 'Library — Book Catalogue')

@section('content')
<div class="dashboard-row" style="grid-template-columns: 2fr 1fr; align-items: start;">
    <div class="glass-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.5rem;">
            <h3 style="font-size:1.1rem; font-weight:700;">Book Catalogue</h3>
            <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                <a href="{{ route('library.borrows') }}" class="btn btn-secondary btn-sm">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    <span>Borrowing Log</span>
                </a>
                <form method="GET" style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" style="width:180px;" placeholder="Search title / author...">
                    <select name="category" class="form-control" style="width:140px;">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        <span>Filter</span>
                    </button>
                </form>
            </div>
        </div>
        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Available</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $book)
                    <tr>
                        <td><strong>{{ $book->title }}</strong><br><span style="font-size:0.75rem; color:var(--text-secondary);">ISBN: {{ $book->isbn ?? 'N/A' }}</span></td>
                        <td>{{ $book->author }}</td>
                        <td><span class="pill pill-info">{{ $book->category }}</span></td>
                        <td>{{ $book->total_copies }}</td>
                        <td style="font-weight:700; color:{{ $book->available_copies > 0 ? 'var(--success-color)' : 'var(--danger-color)' }};">
                            {{ $book->available_copies }}
                        </td>
                        <td>
                            @if($book->available_copies > 0)
                                <span class="pill pill-success">Available</span>
                            @else
                                <span class="pill pill-danger">All Out</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center; color:var(--text-secondary); padding:2rem;">No books registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem;">{{ $books->links() }}</div>
    </div>

    <!-- Add Book Form -->
    <div class="glass-card">
        <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1.5rem;">Register New Book</h3>
        <form action="{{ route('library.books.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Book Title *</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Mathematics for S3">
            </div>
            <div class="form-group">
                <label class="form-label">Author *</label>
                <input type="text" name="author" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">ISBN (Optional)</label>
                <input type="text" name="isbn" class="form-control" placeholder="978-...">
            </div>
            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category" class="form-control" required>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Science">Science</option>
                    <option value="Language">Language & Literature</option>
                    <option value="Humanities">Humanities</option>
                    <option value="Commerce">Commerce & Accounts</option>
                    <option value="Technology">Technology</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Total Copies *</label>
                <input type="number" name="total_copies" class="form-control" min="1" value="1" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="12" y1="9" x2="12" y2="15"/><line x1="9" y1="12" x2="15" y2="12"/></svg>
                <span>Register Book</span>
            </button>
        </form>
    </div>
</div>
@endsection

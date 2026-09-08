@extends('layouts.app')
@section('title', 'Announcements')
@section('page_title', 'School Announcements & Notices')

@section('content')
<div class="dashboard-row" style="grid-template-columns: 2fr 1fr; align-items: start;">
    <!-- Announcements Feed -->
    <div class="glass-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-size:1.1rem; font-weight:700;">Notice Board</h3>
            <form method="GET" style="display:flex; gap:0.5rem;">
                <select name="target_audience" class="form-control" style="width:160px;" onchange="this.form.submit()">
                    <option value="">All Audiences</option>
                    <option value="all"      {{ request('target_audience')=='all'     ?'selected':'' }}>Everyone</option>
                    <option value="parents"  {{ request('target_audience')=='parents'  ?'selected':'' }}>Parents</option>
                    <option value="teachers" {{ request('target_audience')=='teachers' ?'selected':'' }}>Teachers</option>
                    <option value="students" {{ request('target_audience')=='students' ?'selected':'' }}>Students</option>
                </select>
            </form>
        </div>

        @forelse($announcements as $ann)
        @php $expired = $ann->expires_at && $ann->expires_at->isPast(); @endphp
        <div style="border-bottom: 1px solid var(--border-color); padding: 1.25rem 0; @if($expired) opacity:0.5; @endif">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                <div>
                    <span class="pill @if($ann->target_audience=='all') pill-info @elseif($ann->target_audience=='parents') pill-success @elseif($ann->target_audience=='teachers') pill-warning @else pill-danger @endif" style="margin-right:0.5rem; font-size:0.68rem;">
                        {{ ucfirst($ann->target_audience) }}
                    </span>
                    <strong style="font-size:1rem;">{{ $ann->title }}</strong>
                    @if($expired) <span style="font-size:0.72rem; color:var(--danger-color); margin-left:0.5rem;">(Expired)</span> @endif
                </div>
                @can('role:admin,head-teacher,teacher')
                <form action="{{ route('communication.announcements.destroy', $ann->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" style="background:none; border:none; cursor:pointer; color:var(--danger-color); font-size:0.8rem;">Remove</button>
                </form>
                @endcan
            </div>
            <p style="color:var(--text-secondary); font-size:0.9rem; line-height:1.6;">{{ $ann->content }}</p>
            <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:0.5rem;">
                Posted by <strong>{{ $ann->author->full_name ?? 'Admin' }}</strong> · {{ $ann->created_at->diffForHumans() }}
                @if($ann->expires_at && !$expired)
                    · Expires {{ $ann->expires_at->format('d M Y') }}
                @endif
            </div>
        </div>
        @empty
        <div style="text-align:center; color:var(--text-secondary); padding:2rem;">No announcements at this time.</div>
        @endforelse

        <div style="margin-top:1rem;">{{ $announcements->links() }}</div>
    </div>

    <!-- Publish Notice Form (only visible to admin/head-teacher/teacher) -->
    @if(in_array(Auth::user()->role->slug ?? '', ['admin','head-teacher','teacher']))
    <div class="glass-card">
        <h3 style="font-size:1.1rem; font-weight:700; margin-bottom:1.5rem; color:var(--primary-color);">Post Announcement</h3>
        <form action="{{ route('communication.announcements.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Title *</label>
                <input type="text" name="title" class="form-control" required placeholder="Notice title...">
            </div>
            <div class="form-group">
                <label class="form-label">Message *</label>
                <textarea name="content" class="form-control" rows="5" required placeholder="Write your announcement here..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Target Audience *</label>
                <select name="target_audience" class="form-control" required>
                    <option value="all">Everyone</option>
                    <option value="parents">Parents & Guardians</option>
                    <option value="teachers">Teachers & Staff</option>
                    <option value="students">Students</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Expiry Date (Optional)</label>
                <input type="date" name="expires_at" class="form-control" min="{{ now()->addDay()->format('Y-m-d') }}">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                <span>Publish Announcement</span>
            </button>
        </form>
    </div>
    @endif
</div>
@endsection

@extends('layouts.app')
@section('title', 'Database Backups & Disaster Recovery')
@section('page_title', 'Database Backups & Recovery')

@section('content')
<div style="display:flex; flex-direction:column; gap:1.5rem;">

    <!-- Top Action Bar -->
    <div class="glass-card" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; padding:1.25rem 1.5rem;">
        <div>
            <h2 style="font-size:1.25rem; font-weight:800; color:var(--text-primary); margin-bottom:0.25rem; display:flex; align-items:center; gap:0.6rem;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--primary-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 7v8a2 2 0 0 0 2 2h6M8 7V5a2 2 0 0 1 2-2h4.586a1 1 0 0 1 .707.293l4.414 4.414a1 1 0 0 1 .293.707V15a2 2 0 0 1-2 2h-2M8 7H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-2"/>
                </svg>
                Database Snapshots & Cloud Vault
            </h2>
            <p style="font-size:0.85rem; color:var(--text-secondary); margin:0;">
                Automated midnight snapshots, 60-day rolling retention, and Supabase 1 GB free cloud vault sync.
            </p>
        </div>

        <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
            <form action="{{ route('admin.backups.create') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Create Backup Now</span>
                </button>
            </form>

            <button type="button" onclick="document.getElementById('milestoneModal').style.display='flex'" class="btn btn-warning">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                <span>Save Term Milestone</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div style="padding:1rem 1.25rem; border-radius:12px; background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3); color:var(--success-color); font-size:0.9rem; display:flex; align-items:center; gap:0.75rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="padding:1rem 1.25rem; border-radius:12px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); color:var(--danger-color); font-size:0.9rem; display:flex; align-items:center; gap:0.75rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Metric Summary Grid -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,0.15); color:var(--primary-color);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ count($backups) }}</div>
                <div class="stat-label">Total Backups ({{ $dailyCount }} Daily / {{ $milestoneCount }} Milestones)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(6,182,212,0.15); color:var(--info-color);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $formattedTotalSize }}</div>
                <div class="stat-label">Storage Used (~0.1% of 1GB)</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,158,11,0.15); color:var(--warning-color);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value">60 Days</div>
                <div class="stat-label">Rolling Daily Retention</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:{{ $isSupabaseConfigured ? 'rgba(16,185,129,0.15)' : 'rgba(100,116,139,0.15)' }}; color:{{ $isSupabaseConfigured ? 'var(--success-color)' : 'var(--text-secondary)' }};">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"/></svg>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color:{{ $isSupabaseConfigured ? 'var(--success-color)' : 'var(--text-secondary)' }};">
                    {{ $isSupabaseConfigured ? 'Connected' : 'Local Only' }}
                </div>
                <div class="stat-label">{{ $isSupabaseConfigured ? 'Supabase 1 GB Cloud Vault' : 'Configure .env for Cloud' }}</div>
            </div>
        </div>
    </div>

    <!-- Backups Table -->
    <div class="glass-card" style="padding:0; overflow:hidden;">
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.5rem;">
            <h3 style="font-size:1.05rem; font-weight:700; color:var(--text-primary); margin:0;">
                Database Snapshot Archives
            </h3>
            <span style="font-size:0.8rem; color:var(--text-secondary);">
                All archives compressed with Gzip (<code style="color:var(--primary-color);">.sql.gz</code>)
            </span>
        </div>

        <div class="table-container">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Snapshot Filename</th>
                        <th>Classification</th>
                        <th>File Size</th>
                        <th>Created Timestamp</th>
                        <th>Age</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $backup)
                    <tr>
                        <td>
                            <strong style="font-family:monospace; font-size:0.85rem; color:var(--text-primary);">
                                {{ $backup['filename'] }}
                            </strong>
                        </td>
                        <td>
                            @if($backup['is_milestone'])
                                <span class="pill pill-warning" style="font-size:0.75rem; font-weight:700;">
                                    ★ Permanent Milestone
                                </span>
                            @else
                                <span class="pill pill-info" style="font-size:0.75rem;">
                                    Daily Snapshot
                                </span>
                            @endif
                        </td>
                        <td style="font-weight:600; color:var(--text-primary);">
                            {{ $backup['formatted_size'] }}
                        </td>
                        <td style="font-size:0.85rem; color:var(--text-secondary);">
                            {{ $backup['created_at']->format('M d, Y · H:i:s') }}
                        </td>
                        <td style="font-size:0.85rem; color:var(--text-secondary);">
                            {{ $backup['age_days'] < 1 ? 'Today' : $backup['age_days'] . ' days ago' }}
                        </td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:0.4rem; align-items:center;">
                                <a href="{{ route('admin.backups.download', $backup['filename']) }}" class="btn btn-secondary btn-sm" title="Download to PC / Flash Drive">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    <span>Download</span>
                                </a>

                                <form action="{{ route('admin.backups.destroy', $backup['filename']) }}" method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to delete this backup snapshot?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Snapshot" style="padding:0.35rem 0.6rem;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:3rem 1rem; color:var(--text-secondary);">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 0.75rem auto; display:block; opacity:0.5;">
                                <path d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/>
                            </svg>
                            <p style="font-weight:600; margin-bottom:0.25rem;">No database backups generated yet.</p>
                            <span style="font-size:0.8rem;">Click "Create Backup Now" above or wait for the automated midnight scheduler.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Supabase Setup Guide Card -->
    <div class="glass-card" style="background:var(--bg-card); border-left:4px solid var(--success-color); padding:1.5rem;">
        <div style="display:flex; gap:1.25rem; align-items:flex-start;">
            <div style="width:42px; height:42px; border-radius:10px; background:rgba(16,185,129,0.15); color:var(--success-color); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M21.362 9.354H12V.396a.396.396 0 0 0-.716-.233L.117 14.168a.396.396 0 0 0 .307.632H12v8.803a.396.396 0 0 0 .716.233l11.167-13.85a.396.396 0 0 0-.521-.632z"/>
                </svg>
            </div>
            <div style="flex:1;">
                <h4 style="font-size:1rem; font-weight:700; color:var(--text-primary); margin-bottom:0.35rem;">
                    Supabase 1 GB Free Cloud Storage Vault Configuration
                </h4>
                <p style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:0.75rem;">
                    To enable automatic off-site cloud replication for disaster recovery, add your Supabase credentials to your <code style="color:var(--primary-color);">.env</code> file:
                </p>
                <div style="background:rgba(0,0,0,0.25); border:1px solid var(--border-color); border-radius:8px; padding:0.85rem 1rem; font-family:monospace; font-size:0.8rem; color:var(--text-primary); line-height:1.6;">
                    <span style="color:var(--success-color);">SUPABASE_URL</span>=https://&lt;your-project-id&gt;.supabase.co<br>
                    <span style="color:var(--success-color);">SUPABASE_SERVICE_ROLE_KEY</span>=your-supabase-service-role-key<br>
                    <span style="color:var(--success-color);">SUPABASE_STORAGE_BUCKET</span>=school-backups
                </div>
                <p style="font-size:0.8rem; color:var(--text-secondary); margin-top:0.6rem; margin-bottom:0;">
                    💡 <em>Create a private bucket named <code style="color:var(--text-primary);">school-backups</code> in Supabase Storage. With 60-day rolling retention, your cloud backups will use less than 60 MB forever ($0/month).</em>
                </p>
            </div>
        </div>
    </div>

</div>

<!-- Term Milestone Modal -->
<div id="milestoneModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); align-items:center; justify-content:center; padding:1rem;">
    <div class="glass-card" style="max-width:440px; width:100%; padding:1.75rem; box-shadow:var(--shadow-glow);">
        <h3 style="font-size:1.15rem; font-weight:700; color:var(--text-primary); margin-bottom:0.35rem; display:flex; align-items:center; gap:0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--warning-color)" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
            Save Permanent Term Milestone
        </h3>
        <p style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:1.25rem;">
            Milestones are permanent historical snapshots (e.g. End of Term 1 2026) that are <strong>never deleted by automatic 60-day rotation</strong>.
        </p>

        <form action="{{ route('admin.backups.create') }}" method="POST" style="display:flex; flex-direction:column; gap:1rem;">
            @csrf
            <input type="hidden" name="is_milestone" value="1">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Milestone Name / Label *</label>
                <input type="text" name="label" required placeholder="e.g. Term_1_Final_Grades_2026" class="form-control">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:0.75rem; margin-top:0.5rem;">
                <button type="button" onclick="document.getElementById('milestoneModal').style.display='none'" class="btn btn-secondary btn-sm">
                    Cancel
                </button>
                <button type="submit" class="btn btn-warning btn-sm">
                    Create Milestone Snapshot
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

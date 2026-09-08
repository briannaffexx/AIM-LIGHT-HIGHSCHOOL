<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AIM-LIGHT High School')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Prevent FOUC — apply theme before paint
        (function(){
            var t = localStorage.getItem('theme');
            if(t === 'light') document.documentElement.classList.add('light-theme');
            if(localStorage.getItem('sidebar') === 'collapsed') document.documentElement.classList.add('sidebar-collapsed');
        })();
    </script>
    <style>
        /* ============================================================
           SHELL LAYOUT — sidebar + topbar
        ============================================================ */
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}

        :root{
            --sidebar-w: 260px;
            --sidebar-w-sm: 68px;
            --topbar-h: 60px;
        }

        html.light-theme {
            --bg-page:    #f0f2f7;
            --bg-sidebar: #ffffff;
            --bg-card:    #ffffff;
            --text-primary:   #0f172a;
            --text-secondary: #64748b;
            --border-color:   rgba(0,0,0,0.08);
            --sidebar-glow:   rgba(99,102,241,0.07);
            --primary-color:  #4f46e5;
            --primary-gradient: linear-gradient(135deg,#4f46e5,#7c3aed);
            --success-color:  #059669;
            --danger-color:   #dc2626;
            --warning-color:  #d97706;
            --shadow-card:    0 1px 6px rgba(0,0,0,0.06);
            --shadow-glow:    0 4px 24px rgba(0,0,0,0.10);
            --shadow-accent:  0 4px 16px rgba(79,70,229,0.25);
        }
        html:not(.light-theme) {
            --bg-page:    #0d0f1a;
            --bg-sidebar: #111827;
            --bg-card:    #1a2035;
            --text-primary:   #f1f5f9;
            --text-secondary: #94a3b8;
            --border-color:   rgba(255,255,255,0.07);
            --sidebar-glow:   rgba(99,102,241,0.10);
            --primary-color:  #6366f1;
            --primary-gradient: linear-gradient(135deg,#6366f1,#8b5cf6);
            --success-color:  #10b981;
            --danger-color:   #ef4444;
            --warning-color:  #f59e0b;
            --shadow-card:    0 2px 12px rgba(0,0,0,0.35);
            --shadow-glow:    0 8px 40px rgba(0,0,0,0.5);
            --shadow-accent:  0 4px 20px rgba(99,102,241,0.35);
        }

        body{
            font-family:'Inter',sans-serif;
            background: var(--bg-page);
            color: var(--text-primary);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ========= SIDEBAR ========= */
        #sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: width 0.28s cubic-bezier(0.4,0,0.2,1), min-width 0.28s cubic-bezier(0.4,0,0.2,1);
            overflow: hidden;
            position: relative;
            z-index: 200;
            flex-shrink: 0;
        }
        html.sidebar-collapsed #sidebar {
            width: var(--sidebar-w-sm);
            min-width: var(--sidebar-w-sm);
        }

        /* Brand */
        .sb-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0 1.25rem;
            height: var(--topbar-h);
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
            overflow: hidden;
            white-space: nowrap;
        }
        .sb-brand-icon {
            width: 36px; height: 36px;
            background: var(--primary-gradient);
            border-radius: 10px;
            display: flex; align-items:center; justify-content:center;
            font-size: 1rem; font-weight: 800; color: #fff;
            box-shadow: var(--shadow-accent);
            flex-shrink: 0;
        }
        .sb-brand-text { overflow: hidden; }
        .sb-brand-title { font-size: 0.95rem; font-weight: 800; color: var(--text-primary); line-height:1.2; }
        .sb-brand-sub   { font-size: 0.68rem; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.8px; }
        html.sidebar-collapsed .sb-brand-text { display: none; }

        /* Nav scroll area */
        .sb-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem 0.75rem;
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) transparent;
        }
        .sb-nav::-webkit-scrollbar { width: 4px; }
        .sb-nav::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }

        /* Section label */
        .sb-section-label {
            font-size: 0.62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--text-secondary);
            padding: 0.9rem 0.6rem 0.35rem;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s;
        }
        html.sidebar-collapsed .sb-section-label { opacity: 0; }

        /* Nav item */
        .sb-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.62rem 0.75rem;
            border-radius: 10px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.18s ease;
            white-space: nowrap;
            overflow: hidden;
            margin-bottom: 2px;
            position: relative;
        }
        .sb-item svg { flex-shrink: 0; opacity: 0.75; }
        .sb-item span { overflow: hidden; text-overflow: ellipsis; transition: opacity 0.2s; }
        html.sidebar-collapsed .sb-item span { opacity: 0; width: 0; }
        html.sidebar-collapsed .sb-item { justify-content: center; }

        .sb-item:hover {
            color: var(--text-primary);
            background: var(--sidebar-glow);
        }
        .sb-item.active {
            color: var(--primary-color);
            background: var(--sidebar-glow);
            font-weight: 700;
        }
        .sb-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: var(--primary-gradient);
            border-radius: 0 4px 4px 0;
        }
        .sb-item.active svg { opacity: 1; }

        /* Badge pill on nav item */
        .sb-badge {
            margin-left: auto;
            background: var(--primary-gradient);
            color: #fff;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.45rem;
            border-radius: 20px;
            flex-shrink: 0;
            transition: opacity 0.2s;
        }
        html.sidebar-collapsed .sb-badge { opacity: 0; width: 0; padding: 0; }

        /* Tooltip on collapsed */
        html.sidebar-collapsed .sb-item { position: relative; }
        html.sidebar-collapsed .sb-item::after {
            content: attr(data-label);
            position: absolute;
            left: calc(var(--sidebar-w-sm) + 4px);
            top: 50%; transform: translateY(-50%);
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.35rem 0.7rem;
            border-radius: 8px;
            box-shadow: var(--shadow-glow);
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s;
            z-index: 400;
        }
        html.sidebar-collapsed .sb-item:hover::after { opacity: 1; }

        /* Bottom profile block */
        .sb-footer {
            padding: 0.75rem;
            border-top: 1px solid var(--border-color);
            flex-shrink: 0;
        }
        .sb-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.6rem;
            border-radius: 10px;
            cursor: pointer;
            transition: background 0.18s;
            overflow: hidden;
        }
        .sb-user:hover { background: var(--sidebar-glow); }
        .sb-avatar {
            width: 34px; height: 34px;
            background: var(--primary-gradient);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }
        .sb-user-info { overflow: hidden; flex: 1; }
        .sb-user-name  { font-size: 0.82rem; font-weight: 700; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sb-user-role  { font-size: 0.7rem; color: var(--text-secondary); white-space: nowrap; }
        html.sidebar-collapsed .sb-user-info { display: none; }

        /* ========= MAIN AREA ========= */
        #app-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
        }

        /* ========= TOPBAR ========= */
        #topbar {
            height: var(--topbar-h);
            min-height: var(--topbar-h);
            background: var(--bg-sidebar);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0 1.75rem;
            flex-shrink: 0;
            z-index: 100;
        }

        /* Sidebar toggle button */
        .topbar-toggle {
            background: none;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            width: 34px; height: 34px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .topbar-toggle:hover { background: var(--sidebar-glow); color: var(--text-primary); border-color: var(--primary-color); }

        /* Page title in topbar */
        .topbar-title {
            flex: 1;
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Right side actions */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-shrink: 0;
        }

        .tb-icon-btn {
            width: 34px; height: 34px;
            background: none;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            transition: all 0.2s;
            position: relative;
            flex-shrink: 0;
        }
        .tb-icon-btn:hover { background: var(--sidebar-glow); color: var(--text-primary); border-color: var(--primary-color); }

        .tb-notif-badge {
            position: absolute; top: -4px; right: -4px;
            background: #ef4444; color: #fff;
            border-radius: 50%; width: 16px; height: 16px;
            font-size: 0.6rem; font-weight: 700;
            display: none; align-items: center; justify-content: center;
        }

        /* Profile chip */
        .tb-profile {
            display: flex; align-items: center; gap: 0.55rem;
            padding: 0.3rem 0.6rem 0.3rem 0.3rem;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .tb-profile:hover { background: var(--sidebar-glow); border-color: var(--primary-color); }
        .tb-profile-avatar {
            width: 28px; height: 28px;
            background: var(--primary-gradient);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.72rem; font-weight: 800; color: #fff;
        }
        .tb-profile-name { font-size: 0.8rem; font-weight: 600; color: var(--text-primary); }
        .tb-profile-role { font-size: 0.68rem; color: var(--text-secondary); }

        /* Profile dropdown */
        .tb-profile-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: var(--shadow-glow);
            min-width: 220px;
            z-index: 500;
            overflow: hidden;
            display: none;
        }
        .tb-profile-dropdown.open { display: block; animation: slideDown 0.2s ease; }
        .tb-dropdown-header {
            padding: 1rem 1.1rem 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }
        .tb-dropdown-header-name  { font-weight: 700; font-size: 0.9rem; }
        .tb-dropdown-header-email { font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.1rem; }
        .tb-dropdown-item {
            display: flex; align-items: center; gap: 0.7rem;
            padding: 0.7rem 1.1rem;
            font-size: 0.85rem; font-weight: 600;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.15s;
            width: 100%; background: none; border: none; cursor: pointer;
            text-align: left;
        }
        .tb-dropdown-item:hover { background: var(--sidebar-glow); color: var(--text-primary); }
        .tb-dropdown-item.danger  { color: var(--danger-color); }
        .tb-dropdown-item.danger:hover { background: rgba(239,68,68,0.08); }

        @keyframes slideDown {
            from { opacity:0; transform: translateY(-6px); }
            to   { opacity:1; transform: translateY(0); }
        }

        /* Notification dropdown */
        .tb-notif-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: var(--shadow-glow);
            width: 320px;
            z-index: 500;
            overflow: hidden;
            display: none;
        }
        .tb-notif-dropdown.open { display: block; animation: slideDown 0.2s ease; }

        /* ========= CONTENT ========= */
        #content-scroll {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 2rem 2rem;
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) transparent;
        }
        #content-scroll::-webkit-scrollbar { width: 5px; }
        #content-scroll::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 1.75rem;
        }

        /* ========= GLASS CARD (shared) ========= */
        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: var(--shadow-card);
            margin-bottom: 1.5rem;
        }

        /* ========= DASHBOARD GRID ========= */
        .dashboard-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .metric-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.4rem 1.5rem;
            box-shadow: var(--shadow-card);
        }
        .metric-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-secondary); margin-bottom: 0.6rem; }
        .metric-value { font-size: 2rem; font-weight: 800; color: var(--text-primary); line-height: 1; }
        .metric-sub   { font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.35rem; }

        /* ========= TABLE ========= */
        .table-container { overflow-x: auto; border-radius: 10px; }
        .custom-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .custom-table th { padding: 0.75rem 1rem; text-align: left; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: var(--text-secondary); border-bottom: 1px solid var(--border-color); white-space: nowrap; }
        .custom-table td { padding: 0.9rem 1rem; border-bottom: 1px solid var(--border-color); color: var(--text-primary); vertical-align: middle; }
        .custom-table tbody tr:last-child td { border-bottom: none; }
        .custom-table tbody tr:hover td { background: var(--sidebar-glow); }

        /* ========= PILLS ========= */
        .pill { display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
        .pill-success { background: rgba(16,185,129,0.12); color: var(--success-color); }
        .pill-danger  { background: rgba(239,68,68,0.12);  color: var(--danger-color); }
        .pill-warning { background: rgba(245,158,11,0.12); color: var(--warning-color); }
        .pill-info    { background: rgba(99,102,241,0.12); color: var(--primary-color); }

        /* ========= FORMS ========= */
        .form-group  { margin-bottom: 1rem; }
        .form-label  { display: block; font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); margin-bottom: 0.35rem; }
        .form-control{
            width: 100%; padding: 0.6rem 0.85rem;
            background: var(--bg-page);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 0.875rem;
            font-family: inherit;
            transition: border-color 0.18s, box-shadow 0.18s;
            outline: none;
        }
        .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }

        /* ========= BUTTONS ========= */
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; border-radius: 10px; font-size: 0.875rem; font-weight: 700; border: none; cursor: pointer; transition: all 0.2s; text-decoration: none; font-family: inherit; }
        .btn-primary   { background: var(--primary-gradient); color: #fff; box-shadow: var(--shadow-accent); }
        .btn-primary:hover { opacity: 0.88; box-shadow: none; transform: translateY(-1px); }
        .btn-secondary { background: var(--bg-page); border: 1px solid var(--border-color); color: var(--text-primary); }
        .btn-secondary:hover { border-color: var(--primary-color); color: var(--primary-color); }

        /* ========= ALERTS ========= */
        .alert { padding: 0.9rem 1.25rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600; margin-bottom: 1.25rem; border-left: 4px solid; }
        .alert-success { background: rgba(16,185,129,0.08); border-color: var(--success-color); color: var(--success-color); }
        .alert-danger  { background: rgba(239,68,68,0.08);  border-color: var(--danger-color);  color: var(--danger-color); }

        /* ========= RESPONSIVE ========= */
        @media(max-width:768px){
            #sidebar { position: fixed; left: -260px; top:0; bottom:0; transition: left 0.28s ease; z-index: 999; }
            html.sidebar-open #sidebar { left: 0; }
            html.sidebar-open .sb-overlay { display: block; }
            .sb-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:998; }
        }
    </style>
</head>
<body id="body-el">
@php
    $role = Auth::user()->role->slug ?? 'student';
    $userInitials = strtoupper(
        substr(Auth::user()->first_name ?? Auth::user()->name ?? 'U', 0, 1) .
        substr(Auth::user()->last_name ?? '', 0, 1)
    );
@endphp

<!-- Mobile overlay -->
<div class="sb-overlay" onclick="toggleSidebar()"></div>

<!-- ============================================================
     SIDEBAR
============================================================ -->
<nav id="sidebar">

    <!-- Brand -->
    <div class="sb-brand">
        <div class="sb-brand-icon">A</div>
        <div class="sb-brand-text">
            <div class="sb-brand-title">AIM-LIGHT</div>
            <div class="sb-brand-sub">School System</div>
        </div>
    </div>

    <!-- Scrollable Nav -->
    <div class="sb-nav">

        <!-- MAIN -->
        <div class="sb-section-label">Main</div>

        <a href="{{ route('dashboard') }}" class="sb-item {{ Route::is('dashboard') || Route::is('admin.dashboard') || Route::is('headteacher.dashboard') || Route::is('teacher.dashboard') || Route::is('boarding.dashboard') || Route::is('finance.dashboard') || Route::is('auditor.dashboard') || Route::is('student.dashboard') || Route::is('procurement.dashboard') ? 'active' : '' }}" data-label="Dashboard">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('communication.announcements') }}" class="sb-item {{ Route::is('communication.*') ? 'active' : '' }}" data-label="Announcements">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            <span>Announcements</span>
        </a>

        <!-- PEOPLE — Admin / Head Teacher only -->
        @if(in_array($role, ['admin', 'head-teacher']))
        <div class="sb-section-label">People</div>
        <a href="{{ route('students.index') }}" class="sb-item {{ Route::is('students.*') ? 'active' : '' }}" data-label="Students">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>Students</span>
        </a>
        <a href="{{ route('staff.index') }}" class="sb-item {{ Route::is('staff.*') ? 'active' : '' }}" data-label="Staff">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
            <span>Staff</span>
        </a>
        @endif

        <!-- ACADEMICS -->
        @if(in_array($role, ['admin', 'head-teacher', 'teacher']))
        <div class="sb-section-label">Academics</div>
        <a href="{{ route('academics.teacher-subjects') }}" class="sb-item {{ Route::is('academics.*') ? 'active' : '' }}" data-label="Subjects & Marks">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            <span>Subjects & Marks</span>
        </a>
        <a href="{{ route('timetable.index') }}" class="sb-item {{ Route::is('timetable.*') ? 'active' : '' }}" data-label="Timetable">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span>Timetable</span>
        </a>
        @endif

        <!-- DISCIPLINE -->
        @if(in_array($role, ['admin', 'head-teacher', 'teacher', 'boarding-officer', 'warden-matron']))
        <a href="{{ route('discipline.index') }}" class="sb-item {{ Route::is('discipline.*') ? 'active' : '' }}" data-label="Discipline">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span>Discipline Files</span>
        </a>
        @endif

        <!-- BOARDING -->
        @if(in_array($role, ['admin', 'head-teacher', 'boarding-officer', 'warden-matron']))
        <div class="sb-section-label">Boarding</div>
        <a href="{{ route('boarding.rooms') }}" class="sb-item {{ Route::is('boarding.rooms') ? 'active' : '' }}" data-label="Rooms & Beds">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>Rooms & Beds</span>
        </a>
        <a href="{{ route('boarding.attendance') }}" class="sb-item {{ Route::is('boarding.attendance') ? 'active' : '' }}" data-label="Roll Call">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <span>Daily Roll Call</span>
        </a>
        <a href="{{ route('boarding.movements') }}" class="sb-item {{ Route::is('boarding.movements') ? 'active' : '' }}" data-label="Movements">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 3 21 3 21 9"/><polyline points="9 21 3 21 3 15"/><line x1="21" y1="3" x2="14" y2="10"/><line x1="3" y1="21" x2="10" y2="14"/></svg>
            <span>Movements & Leave</span>
        </a>
        <a href="{{ route('boarding.meals') }}" class="sb-item {{ Route::is('boarding.meals') ? 'active' : '' }}" data-label="Meals">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
            <span>Meal Schedules</span>
        </a>
        <a href="{{ route('boarding.incidents') }}" class="sb-item {{ Route::is('boarding.incidents') ? 'active' : '' }}" data-label="Incidents">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>Welfare & Incidents</span>
        </a>
        @endif

        <!-- LIBRARY -->
        @if(in_array($role, ['admin', 'head-teacher', 'teacher', 'student']))
        <div class="sb-section-label">Library</div>
        <a href="{{ route('library.books') }}" class="sb-item {{ Route::is('library.books') ? 'active' : '' }}" data-label="Book Catalogue">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            <span>Book Catalogue</span>
        </a>
        <a href="{{ route('library.borrows') }}" class="sb-item {{ Route::is('library.borrows') ? 'active' : '' }}" data-label="Borrows & Returns">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            <span>Borrows & Returns</span>
        </a>
        @endif

        <!-- INVENTORY / ASSETS -->
        @if(in_array($role, ['admin', 'head-teacher', 'boarding-officer', 'procurement-officer']))
        <div class="sb-section-label">Inventory</div>
        <a href="{{ route('inventory.index') }}" class="sb-item {{ Route::is('inventory.*') ? 'active' : '' }}" data-label="Asset Inventory">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="21 8 21 21 3 21 3 8"/><rect x="1" y="3" width="22" height="5"/><line x1="10" y1="12" x2="14" y2="12"/></svg>
            <span>Asset Inventory</span>
        </a>
        @endif

        <!-- FINANCE -->
        @if(in_array($role, ['admin', 'bursar', 'accountant']))
        <div class="sb-section-label">Finance</div>
        <a href="{{ route('finance.fee-structures') }}" class="sb-item {{ Route::is('finance.fee-structures') ? 'active' : '' }}" data-label="Fee Structures">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <span>Fee Structures</span>
        </a>
        <a href="{{ route('finance.accounts') }}" class="sb-item {{ Route::is('finance.accounts') || Route::is('finance.invoices') ? 'active' : '' }}" data-label="Student Accounts">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            <span>Student Accounts</span>
        </a>
        <a href="{{ route('finance.expenses') }}" class="sb-item {{ Route::is('finance.expenses') ? 'active' : '' }}" data-label="Cashflow">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            <span>Cashflow</span>
        </a>
        <a href="{{ route('finance.budgets') }}" class="sb-item {{ Route::is('finance.budgets') ? 'active' : '' }}" data-label="Budgets">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            <span>Budgets</span>
        </a>
        @endif

        <!-- PROCUREMENT -->
        @if(in_array($role, ['admin', 'bursar', 'accountant', 'procurement-officer']))
        <div class="sb-section-label">Procurement</div>
        <a href="{{ route('finance.procurement') }}" class="sb-item {{ Route::is('finance.procurement') ? 'active' : '' }}" data-label="Purchase Orders">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <span>Purchase Orders</span>
        </a>
        @endif

        <!-- AUDIT & SYSTEM RECOVERY -->
        @if(in_array($role, ['admin', 'auditor']))
        <div class="sb-section-label">Compliance</div>
        <a href="{{ route('auditor.logs') }}" class="sb-item {{ Route::is('auditor.*') ? 'active' : '' }}" data-label="Audit Logs">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            <span>Audit Logs</span>
        </a>
        @endif

        @if(in_array($role, ['admin', 'head-teacher']))
        <a href="{{ route('admin.backups.index') }}" class="sb-item {{ Route::is('admin.backups.*') ? 'active' : '' }}" data-label="System Backups">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7v8a2 2 0 0 0 2 2h6M8 7V5a2 2 0 0 1 2-2h4.586a1 1 0 0 1 .707.293l4.414 4.414a1 1 0 0 1 .293.707V15a2 2 0 0 1-2 2h-2M8 7H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-2"/></svg>
            <span>System Backups</span>
        </a>
        @endif

        <!-- PARENT PORTAL -->
        @if($role === 'parent')
        <div class="sb-section-label">My Family</div>
        <a href="{{ route('parent.dashboard') }}" class="sb-item {{ Route::is('parent.*') ? 'active' : '' }}" data-label="My Children">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span>My Children</span>
        </a>
        @endif

    </div>

    <!-- Footer: user profile -->
    <div class="sb-footer">
        <a href="{{ route('profile.settings') }}" class="sb-user" style="text-decoration:none;">
            <div class="sb-avatar">{{ $userInitials }}</div>
            <div class="sb-user-info">
                <div class="sb-user-name">{{ Auth::user()->first_name ?? Auth::user()->name }}</div>
                <div class="sb-user-role">{{ Auth::user()->role->name ?? 'Staff' }}</div>
            </div>
        </a>
    </div>

</nav>

<!-- ============================================================
     MAIN BODY (topbar + content)
============================================================ -->
<div id="app-body">

    <!-- TOP BAR -->
    <header id="topbar">
        <!-- Sidebar toggle -->
        <button class="topbar-toggle" onclick="toggleSidebar()" title="Toggle sidebar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>

        <!-- Page title -->
        <div class="topbar-title">@yield('page_title', 'Dashboard')</div>

        <!-- Right actions -->
        <div class="topbar-right">

            <!-- Theme toggle -->
            <button type="button" class="tb-icon-btn" id="theme-toggle-trigger" title="Toggle theme">
                <svg id="theme-icon-sun" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <svg id="theme-icon-moon" style="display:none;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>

            <!-- Notifications -->
            <div style="position:relative;">
                <button class="tb-icon-btn" id="notif-btn" onclick="toggleNotif()" title="Notifications">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <span id="notif-badge" class="tb-notif-badge">0</span>
                </button>
                <div id="notif-dropdown" class="tb-notif-dropdown">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:0.9rem 1.1rem;border-bottom:1px solid var(--border-color);">
                        <span style="font-weight:700;font-size:0.9rem;">Notifications</span>
                        <button onclick="markAllRead()" style="background:none;border:none;color:var(--primary-color);font-size:0.78rem;cursor:pointer;font-weight:600;">Mark all read</button>
                    </div>
                    <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>
                    <div id="notif-empty" style="display:none;padding:2rem;text-align:center;color:var(--text-secondary);font-size:0.85rem;font-style:italic;">No notifications</div>
                </div>
            </div>

            <!-- Profile chip -->
            <div style="position:relative;">
                <div class="tb-profile" id="profile-trigger" onclick="toggleProfile()">
                    <div class="tb-profile-avatar">{{ $userInitials }}</div>
                    <div>
                        <div class="tb-profile-name">{{ Auth::user()->first_name ?? Auth::user()->name }}</div>
                        <div class="tb-profile-role">{{ Auth::user()->role->name ?? '' }}</div>
                    </div>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-secondary);"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="tb-profile-dropdown" id="profile-dropdown">
                    <div class="tb-dropdown-header">
                        <div class="tb-dropdown-header-name">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                        <div class="tb-dropdown-header-email">{{ Auth::user()->email }}</div>
                    </div>
                    <a href="{{ route('profile.settings') }}" class="tb-dropdown-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 17H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Account Settings
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="width:100%;">
                        @csrf
                        <button type="submit" class="tb-dropdown-item danger">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    <!-- CONTENT AREA -->
    <div id="content-scroll">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul style="list-style:none;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<!-- ============================================================
     SCRIPTS
============================================================ -->
<script>
    // ---- Sidebar collapse ----
    function toggleSidebar() {
        const html = document.documentElement;
        const collapsed = html.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebar', collapsed ? 'collapsed' : 'expanded');
    }

    // ---- Theme ----
    const bodyEl    = document.getElementById('body-el');
    const sunIcon   = document.getElementById('theme-icon-sun');
    const moonIcon  = document.getElementById('theme-icon-moon');

    function syncThemeIcons() {
        const isLight = document.documentElement.classList.contains('light-theme');
        sunIcon.style.display  = isLight ? 'none'  : 'block';
        moonIcon.style.display = isLight ? 'block' : 'none';
    }
    syncThemeIcons();

    document.getElementById('theme-toggle-trigger').addEventListener('click', function() {
        const html = document.documentElement;
        const isLight = html.classList.toggle('light-theme');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        syncThemeIcons();
    });

    // ---- Profile dropdown ----
    function toggleProfile() {
        document.getElementById('profile-dropdown').classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        const trigger  = document.getElementById('profile-trigger');
        const dropdown = document.getElementById('profile-dropdown');
        if (dropdown && trigger && !trigger.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
        const nd = document.getElementById('notif-dropdown');
        const nb = document.getElementById('notif-btn');
        if (nd && nb && !nb.contains(e.target) && !nd.contains(e.target)) {
            nd.classList.remove('open');
        }
    });

    // ---- Notifications ----
    function toggleNotif() {
        const dd = document.getElementById('notif-dropdown');
        if (dd.classList.toggle('open')) loadNotifications();
    }

    function loadNotifications() {
        fetch('/notifications', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('notif-badge');
                const list  = document.getElementById('notif-list');
                const empty = document.getElementById('notif-empty');

                if (data.unread_count > 0) {
                    badge.style.display = 'flex';
                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                } else {
                    badge.style.display = 'none';
                }

                if (data.notifications.length === 0) {
                    list.innerHTML = '';
                    empty.style.display = 'block';
                } else {
                    empty.style.display = 'none';
                    list.innerHTML = data.notifications.map(n => `
                        <div onclick="markRead(${n.id},'${n.link||''}')"
                             style="padding:.75rem 1.1rem;border-bottom:1px solid var(--border-color);cursor:pointer;background:${n.read?'transparent':'rgba(99,102,241,0.06)'};"
                             onmouseover="this.style.background='rgba(99,102,241,0.1)'"
                             onmouseout="this.style.background='${n.read?'transparent':'rgba(99,102,241,0.06)'}'">
                            <div style="font-weight:700;font-size:.83rem;display:flex;align-items:center;gap:.4rem;">
                                ${!n.read?'<span style="width:6px;height:6px;border-radius:50%;background:var(--primary-color);display:inline-block;"></span>':''}
                                ${n.title}
                            </div>
                            <div style="font-size:.78rem;color:var(--text-secondary);margin-top:.2rem;">${n.message}</div>
                            <div style="font-size:.72rem;color:var(--text-secondary);opacity:.6;margin-top:.1rem;">${n.time}</div>
                        </div>
                    `).join('');
                }
            }).catch(()=>{});
    }

    function markRead(id, link) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content||'', 'Accept': 'application/json' }
        }).then(() => { if (link) window.location.href = link; else loadNotifications(); });
    }

    function markAllRead() {
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content||'', 'Accept': 'application/json' }
        }).then(() => loadNotifications());
    }

    setInterval(() => {
        fetch('/notifications', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json()).then(data => {
                const badge = document.getElementById('notif-badge');
                if (!badge) return;
                if (data.unread_count > 0) { badge.style.display='flex'; badge.textContent=data.unread_count>9?'9+':data.unread_count; }
                else { badge.style.display='none'; }
            }).catch(()=>{});
    }, 60000);
</script>
@stack('scripts')
</body>
</html>

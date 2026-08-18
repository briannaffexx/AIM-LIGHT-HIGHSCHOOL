<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AIM-LIGHT High School')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Apply theme immediately to prevent flashing
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-theme');
        }
    </script>
</head>
<body id="body-el">
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.getElementById('body-el').classList.add('light-theme');
        }
    </script>

    <!-- Top Horizontal Navigation Bar -->
    <header class="top-nav-bar">
        <div class="top-nav-container">
            <!-- Logo Section -->
            <div class="logo-container" style="display: flex; align-items: center; gap: 0.5rem;">
                <img src="{{ asset('images/logo.jpg') }}" alt="AIM-LIGHT Logo" style="width: 32px; height: 32px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.15); object-fit: cover;">
                <div>
                    <div class="logo-text">AIM-LIGHT</div>
                    <div class="role-badge">{{ Auth::user()->role->name ?? 'Student' }}</div>
                </div>
            </div>

            <!-- Horizontal Menu Links -->
            <nav>
                <ul class="top-nav-menu">
                    @php
                        $role = Auth::user()->role->slug ?? 'student';
                    @endphp

                    <!-- Dashboard Link -->
                    <li class="top-nav-item">
                        <a href="{{ route('dashboard') }}" class="top-nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            Dashboard
                        </a>
                    </li>

                    <!-- Registries Dropdown -->
                    @if(in_array($role, ['admin', 'head-teacher']))
                        <li class="top-nav-item">
                            <a href="#" class="top-nav-link {{ Route::is('students.*') || Route::is('staff.*') ? 'active' : '' }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
                                Registries
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 0.15rem;"><polyline points="6 9 12 15 18 9"/></svg>
                            </a>
                            <div class="top-nav-dropdown">
                                <a href="{{ route('students.index') }}" class="top-nav-dropdown-item {{ Route::is('students.*') ? 'active' : '' }}">Students Directory</a>
                                <a href="{{ route('staff.index') }}" class="top-nav-dropdown-item {{ Route::is('staff.*') ? 'active' : '' }}">Staff Directory</a>
                            </div>
                        </li>
                    @endif

                    <!-- Academics Link -->
                    @if(in_array($role, ['teacher', 'admin']))
                        <li class="top-nav-item">
                            <a href="{{ route('academics.teacher-subjects') }}" class="top-nav-link {{ Route::is('academics.teacher-subjects') || Route::is('academics.assessments') || Route::is('academics.marks') ? 'active' : '' }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                Academics
                            </a>
                        </li>
                    @endif

                    <!-- Boarding Ops Dropdown -->
                    @if(in_array($role, ['boarding-officer', 'warden-matron', 'admin', 'head-teacher']))
                        <li class="top-nav-item">
                            <a href="#" class="top-nav-link {{ Route::is('boarding.*') ? 'active' : '' }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                Boarding Ops
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 0.15rem;"><polyline points="6 9 12 15 18 9"/></svg>
                            </a>
                            <div class="top-nav-dropdown">
                                <a href="{{ route('boarding.rooms') }}" class="top-nav-dropdown-item {{ Route::is('boarding.rooms') ? 'active' : '' }}">Rooms & Beds</a>
                                <a href="{{ route('boarding.attendance') }}" class="top-nav-dropdown-item {{ Route::is('boarding.attendance') ? 'active' : '' }}">Daily Roll Call</a>
                                <a href="{{ route('boarding.movements') }}" class="top-nav-dropdown-item {{ Route::is('boarding.movements') ? 'active' : '' }}">Movements & Leaves</a>
                                <a href="{{ route('boarding.meals') }}" class="top-nav-dropdown-item {{ Route::is('boarding.meals') ? 'active' : '' }}">Meal Schedules</a>
                                <a href="{{ route('boarding.incidents') }}" class="top-nav-dropdown-item {{ Route::is('boarding.incidents') ? 'active' : '' }}">Incidents & Welfare</a>
                                <a href="{{ route('boarding.resources') }}" class="top-nav-dropdown-item {{ Route::is('boarding.resources') ? 'active' : '' }}">Inventory Resources</a>
                            </div>
                        </li>
                    @endif

                    <!-- Financials Dropdown -->
                    @if(in_array($role, ['bursar', 'accountant', 'admin', 'head-teacher', 'procurement-officer']))
                        <li class="top-nav-item">
                            <a href="#" class="top-nav-link {{ Route::is('finance.*') ? 'active' : '' }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                Finance & Logistics
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 0.15rem;"><polyline points="6 9 12 15 18 9"/></svg>
                            </a>
                            <div class="top-nav-dropdown">
                                @if(in_array($role, ['bursar', 'accountant', 'admin', 'head-teacher']))
                                    <a href="{{ route('finance.fee-structures') }}" class="top-nav-dropdown-item {{ Route::is('finance.fee-structures') ? 'active' : '' }}">Fee Structures</a>
                                    <a href="{{ route('finance.accounts') }}" class="top-nav-dropdown-item {{ Route::is('finance.accounts') || Route::is('finance.invoices') ? 'active' : '' }}">Student Accounts</a>
                                    <a href="{{ route('finance.expenses') }}" class="top-nav-dropdown-item {{ Route::is('finance.expenses') ? 'active' : '' }}">General Cashflow</a>
                                    <a href="{{ route('finance.budgets') }}" class="top-nav-dropdown-item {{ Route::is('finance.budgets') ? 'active' : '' }}">Budgets</a>
                                @endif
                                <a href="{{ route('finance.procurement') }}" class="top-nav-dropdown-item {{ Route::is('finance.procurement') ? 'active' : '' }}">Procurement / POs</a>
                            </div>
                        </li>
                    @endif

                    <!-- Auditor Link -->
                    @if(in_array($role, ['auditor', 'admin']))
                        <li class="top-nav-item">
                            <a href="{{ route('auditor.logs') }}" class="top-nav-link {{ Route::is('auditor.logs') ? 'active' : '' }}">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                                Ledger Audits
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>

        <!-- Top Right Actions -->
        <div class="topbar-actions">
            <!-- Notifications -->
            <div class="notification-wrapper" style="position: relative;" id="notif-wrapper">
                <button class="icon-btn" id="notif-btn" onclick="toggleNotifDropdown()" title="Notifications"
                    style="position: relative; background: transparent; border: 1px solid var(--border-color); border-radius: 8px; width: 38px; height: 38px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-secondary); transition: all 0.2s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <span id="notif-badge" style="display:none; position:absolute; top:-4px; right:-4px; background:#ef4444; color:#fff; border-radius:50%; width:16px; height:16px; font-size:0.6rem; font-weight:700; align-items:center; justify-content:center;">0</span>
                </button>

                <div id="notif-dropdown" style="display:none; position:absolute; right:0; top:calc(100% + 8px); width:320px; background:var(--bg-card); border:1px solid var(--border-color); border-radius:12px; box-shadow:0 8px 32px rgba(0,0,0,0.3); z-index:1000; overflow:hidden;">
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.9rem 1rem; border-bottom:1px solid var(--border-color);">
                        <span style="font-weight:600; font-size:0.9rem;">Notifications</span>
                        <button onclick="markAllRead()" style="background:none; border:none; color:var(--primary-color); font-size:0.8rem; cursor:pointer;">Mark all read</button>
                    </div>
                    <div id="notif-list" style="max-height:320px; overflow-y:auto;"></div>
                    <div id="notif-empty" style="display:none; padding:2rem; text-align:center; color:var(--text-secondary); font-size:0.85rem; font-style:italic;">No notifications</div>
                </div>
            </div>

            <!-- Theme Toggle Icon Button -->
            <button type="button" class="theme-toggle-btn" id="theme-toggle-trigger" title="Toggle Light/Dark Theme">
                <!-- Sun Icon (shown in dark mode to switch to light) -->
                <svg id="theme-icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                <!-- Moon Icon (shown in light mode to switch to dark) -->
                <svg id="theme-icon-moon" style="display: none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
            </button>

            <!-- Profile Settings Dropdown Menu -->
            <div class="profile-dropdown-container">
                <div class="profile-trigger" id="profile-menu-trigger">
                    <div class="profile-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                    <div class="profile-details">
                        <div class="profile-name">{{ Auth::user()->name }}</div>
                        <div class="profile-role">{{ Auth::user()->role->name }}</div>
                    </div>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                <!-- Dropdown Content -->
                <div class="dropdown-menu" id="profile-dropdown-menu">
                    <div class="dropdown-header">
                        <div class="dropdown-header-name">{{ Auth::user()->name }}</div>
                        <div class="dropdown-header-email">{{ Auth::user()->email }}</div>
                    </div>
                    
                    <a href="{{ route('profile.settings') }}" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        Account Settings
                    </a>

                    <form action="{{ route('logout') }}" method="POST" style="width: 100%;">
                        @csrf
                        <button type="submit" class="dropdown-item dropdown-item-danger">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Content Body -->
        <main class="content-body">
            <h2 class="section-title" style="font-size: 1.65rem; font-weight: 800; margin-bottom: 2rem; background: linear-gradient(135deg, var(--text-primary) 30%, var(--text-secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">@yield('page_title', 'School Panel')</h2>
            
            @if(session('success'))
                <div class="glass-card" style="border-left: 4px solid var(--success-color); margin-bottom: 1.5rem; padding: 1rem 1.5rem; color: var(--success-color);">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="glass-card" style="border-left: 4px solid var(--danger-color); margin-bottom: 1.5rem; padding: 1rem 1.5rem; color: var(--danger-color);">
                    <ul style="list-style: none;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Dropdown menu and Theme switching functionality script -->
    <script>
        // Dropdown Menu Toggle
        const profileTrigger = document.getElementById('profile-menu-trigger');
        const profileMenu = document.getElementById('profile-dropdown-menu');

        profileTrigger.addEventListener('click', function(e) {
            e.stopPropagation();
            profileMenu.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!profileMenu.contains(e.target) && !profileTrigger.contains(e.target)) {
                profileMenu.classList.remove('show');
            }
        });

        // Theme Switch Toggle
        const themeBtn = document.getElementById('theme-toggle-trigger');
        const bodyEl = document.getElementById('body-el');
        const sunIcon = document.getElementById('theme-icon-sun');
        const moonIcon = document.getElementById('theme-icon-moon');

        function updateThemeIcons() {
            if (bodyEl.classList.contains('light-theme')) {
                sunIcon.style.display = 'none';
                moonIcon.style.display = 'block';
            } else {
                sunIcon.style.display = 'block';
                moonIcon.style.display = 'none';
            }
        }

        // Initialize icons on page load
        updateThemeIcons();

        themeBtn.addEventListener('click', function() {
            if (bodyEl.classList.contains('light-theme')) {
                bodyEl.classList.remove('light-theme');
                document.documentElement.classList.remove('light-theme');
                localStorage.setItem('theme', 'dark');
            } else {
                bodyEl.classList.add('light-theme');
                document.documentElement.classList.add('light-theme');
                localStorage.setItem('theme', 'light');
            }
            updateThemeIcons();
        });
    </script>

    <script>
    // ===== NOTIFICATIONS =====
    function toggleNotifDropdown() {
        const dd = document.getElementById('notif-dropdown');
        if (dd.style.display === 'none') {
            dd.style.display = 'block';
            loadNotifications();
        } else {
            dd.style.display = 'none';
        }
    }

    document.addEventListener('click', function(e) {
        const wrapper = document.getElementById('notif-wrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            const dd = document.getElementById('notif-dropdown');
            if (dd) dd.style.display = 'none';
        }
    });

    function loadNotifications() {
        fetch('/notifications', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('notif-badge');
                const list = document.getElementById('notif-list');
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
                        <div onclick="markRead(${n.id}, '${n.link || ''}')"
                             style="padding:0.75rem 1rem; border-bottom:1px solid var(--border-color); cursor:pointer; transition:background 0.15s; background:${n.read ? 'transparent' : 'rgba(99,102,241,0.06)'};"
                             onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='${n.read ? 'transparent' : 'rgba(99,102,241,0.06)'}'">
                            <div style="font-weight:600; font-size:0.85rem; margin-bottom:0.2rem; display:flex; align-items:center; gap:0.5rem;">
                                ${!n.read ? '<span style="width:6px;height:6px;border-radius:50%;background:#6366f1;display:inline-block;"></span>' : ''}
                                ${n.title}
                            </div>
                            <div style="font-size:0.78rem; color:var(--text-secondary); margin-bottom:0.2rem;">${n.message}</div>
                            <div style="font-size:0.72rem; color:var(--text-secondary); opacity:0.6;">${n.time}</div>
                        </div>
                    `).join('');
                }
            })
            .catch(() => {});
    }

    function markRead(id, link) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' }
        }).then(() => {
            if (link) window.location.href = link;
            else loadNotifications();
        });
    }

    function markAllRead() {
        fetch('/notifications/read-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Accept': 'application/json' }
        }).then(() => loadNotifications());
    }

    // Poll for new notifications every 60 seconds
    setInterval(() => {
        const badge = document.getElementById('notif-badge');
        if (badge) {
            fetch('/notifications', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.json())
                .then(data => {
                    if (data.unread_count > 0) {
                        badge.style.display = 'flex';
                        badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;
                    } else {
                        badge.style.display = 'none';
                    }
                }).catch(() => {});
        }
    }, 60000);
    </script>

@stack('scripts')
</body>
</html>

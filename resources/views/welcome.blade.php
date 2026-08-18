<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - AIM-LIGHT High School</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light-theme');
        }
    </script>
    <style>
        /* ========== PROFESSIONAL ANIMATED LANDING ========== */
        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Scroll Progress Bar */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: linear-gradient(90deg, #6366f1, #4f46e5, #4338ca);
            z-index: 10000;
            transition: width 0.1s ease;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-main);
        }
        ::-webkit-scrollbar-thumb {
            background: #6366f1;
            border-radius: 10px;
        }

        body {
            animation: fadeInPage 1s ease;
            cursor: none;
            overflow-x: hidden;
        }
        @media (max-width: 768px) {
            body { cursor: auto; }
        }

        @keyframes fadeInPage {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Preloader */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-main);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        .preloader.hidden {
            opacity: 0;
            visibility: hidden;
        }
        .preloader-logo {
            width: 60px;
            height: 60px;
            border: 4px solid #6366f1;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        .preloader-text {
            font-size: 1rem;
            font-weight: 600;
            color: #6366f1;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: pulseText 1.5s ease-in-out infinite;
        }
        @keyframes pulseText {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Cursor Elements */
        .cursor-glow {
            position: fixed;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.08) 0%, transparent 70%);
            pointer-events: none;
            z-index: 9998;
            transition: transform 0.15s ease;
        }
        .cursor-dot {
            position: fixed;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #6366f1;
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.05s ease;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.5);
        }

        /* Aurora Background */
        .aurora {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }
        .aurora-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.2;
            animation: auroraMove 12s infinite ease-in-out;
        }
        .aurora-blob-1 {
            width: 600px;
            height: 600px;
            background: #6366f1;
            top: -200px;
            left: -200px;
        }
        .aurora-blob-2 {
            width: 500px;
            height: 500px;
            background: #4f46e5;
            bottom: -150px;
            right: -150px;
            animation-delay: 4s;
        }
        .aurora-blob-3 {
            width: 400px;
            height: 400px;
            background: #3b82f6;
            top: 40%;
            left: 55%;
            animation-delay: 8s;
        }
        @keyframes auroraMove {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(50px, -50px) scale(1.1); }
            50% { transform: translate(-30px, 30px) scale(0.9); }
            75% { transform: translate(20px, -20px) scale(1.05); }
        }

        /* Particle Canvas */
        #particleCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .landing-container {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* Navbar */
        .navbar {
            position: sticky;
            top: 1rem;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            margin-bottom: 3rem;
            animation: navbarSlide 0.8s ease;
        }
        @keyframes navbarSlide {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        body.light-theme .navbar {
            background: rgba(255, 255, 255, 0.7);
        }
        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .live-clock {
            font-size: 0.85rem;
            font-weight: 600;
            color: #6366f1;
            letter-spacing: 1px;
        }

        /* Bento Grid */
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.5rem;
            margin-bottom: 4rem;
        }

        .bento-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
            animation: cardAppear 0.8s ease forwards;
            opacity: 0;
        }
        @keyframes cardAppear {
            from { opacity: 0; transform: translateY(40px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        body.light-theme .bento-card {
            background: rgba(255, 255, 255, 0.6);
        }

        /* Hero Card */
        .hero-card {
            grid-column: span 7;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(79, 70, 229, 0.05) 100%);
            animation-delay: 0.2s;
        }
        .hero-greeting {
            font-size: 0.9rem;
            color: #6366f1;
            margin-bottom: 1rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            animation: fadeInUp 0.8s ease;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            min-height: 4rem;
        }
        .typed-text {
            background: linear-gradient(135deg, var(--text-primary) 0%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .cursor-blink {
            display: inline-block;
            width: 3px;
            height: 1.2em;
            background: #6366f1;
            animation: blink 0.7s infinite;
            vertical-align: text-bottom;
            margin-left: 4px;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .hero-desc {
            font-size: 1.1rem;
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 2rem;
        }
        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        /* Professional Button */
        .btn-professional {
            position: relative;
            display: inline-block;
            padding: 0.9rem 2rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            border-radius: 50px;
            text-decoration: none;
            transition: box-shadow 0.3s ease;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
            letter-spacing: 0.5px;
        }

        /* Stats Card */
        .stats-card {
            grid-column: span 5;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 1.5rem;
            animation-delay: 0.4s;
        }
        .stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 0;
            border-bottom: 1px solid var(--border-color);
            border-radius: 12px;
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .stat-row:last-child {
            border-bottom: none;
        }
        .stat-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #10b981;
            animation: pulseDot 2s infinite;
        }
        .stat-row:nth-child(2) .stat-dot { animation-delay: 0.3s; }
        .stat-row:nth-child(3) .stat-dot { animation-delay: 0.6s; }
        .stat-row:nth-child(4) .stat-dot { animation-delay: 0.9s; }
        @keyframes pulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.5; }
        }
        .stat-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }
        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #6366f1;
        }

        /* Feature Cards */
        .feature-card {
            grid-column: span 3;
            min-height: 250px;
            display: flex;
            flex-direction: column;
        }
        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(99, 102, 241, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6366f1;
            margin-bottom: 1.5rem;
        }
        .feature-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }
        .feature-desc {
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Role Cards */
        .role-card {
            grid-column: span 6;
            min-height: 160px;
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .role-emoji {
            font-size: 3.5rem;
            flex-shrink: 0;
        }
        .role-info {
            flex: 1;
        }
        .role-name {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .role-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* CTA Card */
        .cta-card {
            grid-column: span 12;
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(79, 70, 229, 0.08) 100%);
        }
        .cta-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .cta-desc {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 3rem 0;
            color: var(--text-secondary);
            font-size: 0.875rem;
            border-top: 1px solid var(--border-color);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .hero-card { grid-column: span 12; }
            .stats-card { grid-column: span 12; }
            .feature-card { grid-column: span 6; }
            .role-card { grid-column: span 12; }
        }
        @media (max-width: 640px) {
            .feature-card { grid-column: span 12; }
            .hero-title { font-size: 2.5rem; }
        }
    </style>
</head>
<body id="body-el" style="display: block; overflow-y: auto; background-color: var(--bg-main);">
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.getElementById('body-el').classList.add('light-theme');
        }
    </script>

    <!-- Scroll Progress -->
    <div class="scroll-progress" id="scrollProgress"></div>

    <!-- Preloader -->
    <div class="preloader" id="preloader">
        <div class="preloader-logo"></div>
        <div class="preloader-text">Loading System</div>
    </div>

    <!-- Cursor Elements -->
    <div class="cursor-glow" id="cursorGlow"></div>
    <div class="cursor-dot" id="cursorDot"></div>

    <!-- Aurora Background -->
    <div class="aurora">
        <div class="aurora-blob aurora-blob-1"></div>
        <div class="aurora-blob aurora-blob-2"></div>
        <div class="aurora-blob aurora-blob-3"></div>
    </div>

    <!-- Particle Canvas -->
    <canvas id="particleCanvas"></canvas>

    <div class="landing-container">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="logo-container" style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem;">
                <img src="{{ asset('images/logo.jpg') }}" alt="AIM-LIGHT Logo" style="width: 38px; height: 38px; border-radius: 6px; border: 1.5px solid rgba(255,255,255,0.15); object-fit: cover;">
                <div class="logo-text">AIM-LIGHT</div>
            </div>

            <div class="navbar-actions">
                <span class="live-clock" id="liveClock"></span>
                <button type="button" class="theme-toggle-btn" id="theme-toggle-trigger" title="Toggle Light/Dark Theme">
                    <svg id="theme-icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    <svg id="theme-icon-moon" style="display: none;" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary" style="border-radius: 50px;">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary" style="border-radius: 50px;">Sign In</a>
                    @endauth
                @endif
            </div>
        </nav>

        <!-- Bento Grid -->
        <div class="bento-grid">
            <!-- Hero Card -->
            <div class="bento-card hero-card">
                <div class="hero-greeting" id="heroGreeting"></div>
                <h1 class="hero-title">
                    <span class="typed-text" id="typedText"></span>
                    <span class="cursor-blink"></span>
                </h1>
                <p class="hero-desc" style="font-weight: 700; color: var(--primary-color); letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 0.5rem;">A Commitment to Excellence</p>
                <p class="hero-desc" style="margin-top: 0;">An all-in-one administration panel designed for modern boarding institutions. Streamline student tracking, room allocations, meal schedules, finance tracking, and audits in one integrated system.</p>
                <div class="hero-actions">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-professional">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-professional">Enter Admin Portal</a>
                    @endauth
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bento-card stats-card">
                <div class="stat-row">
                    <div class="stat-info">
                        <span class="stat-dot"></span>
                        <span class="stat-label">Occupied Beds</span>
                    </div>
                    <span class="stat-value">87%</span>
                </div>
                <div class="stat-row">
                    <div class="stat-info">
                        <span class="stat-dot"></span>
                        <span class="stat-label">Active Leave Requests</span>
                    </div>
                    <span class="stat-value">12 Pending</span>
                </div>
                <div class="stat-row">
                    <div class="stat-info">
                        <span class="stat-dot"></span>
                        <span class="stat-label">Procurement Orders</span>
                    </div>
                    <span class="stat-value">5 Approved</span>
                </div>
                <div class="stat-row">
                    <div class="stat-info">
                        <span class="stat-dot"></span>
                        <span class="stat-label">Roll Call Compliance</span>
                    </div>
                    <span class="stat-value" style="color: #10b981;">100% Verified</span>
                </div>
            </div>

            <!-- Feature Cards -->
            <div class="bento-card feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
                <h3 class="feature-title">Academic & Marks</h3>
                <p class="feature-desc">Assign subjects, manage assessments, input grades, and generate student report cards instantly.</p>
            </div>

            <div class="bento-card feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <h3 class="feature-title">Boarding & Dormitories</h3>
                <p class="feature-desc">Track room capacity, allocate beds, register roll call, manage student leave, and schedule meals.</p>
            </div>

            <div class="bento-card feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="feature-title">Financial Control</h3>
                <p class="feature-desc">Configure fee structures, log payments, track expenditure, and manage procurement requests.</p>
            </div>

            <div class="bento-card feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <h3 class="feature-title">Internal Audits</h3>
                <p class="feature-desc">Fully logged trails of payments, expenditures, and logs for clean auditing and compliance.</p>
            </div>

            <!-- Role Cards -->
            <div class="bento-card role-card">
                <div class="role-emoji">👨‍🏫</div>
                <div class="role-info">
                    <h3 class="role-name">Teachers</h3>
                    <p class="role-desc">Manage subjects, assessments, and student marks with dedicated dashboards.</p>
                </div>
            </div>

            <div class="bento-card role-card">
                <div class="role-emoji">🏠</div>
                <div class="role-info">
                    <h3 class="role-name">Boarding Officers</h3>
                    <p class="role-desc">Oversee dorms, attendance, and student movements in real time.</p>
                </div>
            </div>

            <div class="bento-card role-card">
                <div class="role-emoji">💰</div>
                <div class="role-info">
                    <h3 class="role-name">Finance Staff</h3>
                    <p class="role-desc">Track fees, payments, budgets, and procurement requests efficiently.</p>
                </div>
            </div>

            <div class="bento-card role-card">
                <div class="role-emoji">🔍</div>
                <div class="role-info">
                    <h3 class="role-name">Auditors</h3>
                    <p class="role-desc">Review logs and ensure financial transparency with full audit trails.</p>
                </div>
            </div>

            <!-- CTA Card -->
            <div class="bento-card cta-card">
                <h2 class="cta-title">Ready to Transform Your School?</h2>
                <p class="cta-desc">Join the next generation of boarding school management. Experience the power of integrated administration.</p>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-professional" style="padding: 1rem 2.5rem; font-size: 1rem;">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-professional" style="padding: 1rem 2.5rem; font-size: 1rem;">Get Started</a>
                @endauth
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            &copy; {{ date('Y') }} AIM-LIGHT High School. All rights reserved.
        </footer>
    </div>

    <script>
        // ============ PRELOADER ============
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('preloader').classList.add('hidden');
            }, 600);
        });

        // ============ SCROLL PROGRESS ============
        const scrollProgress = document.getElementById('scrollProgress');
        window.addEventListener('scroll', function() {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = (scrollTop / docHeight) * 100;
            scrollProgress.style.width = progress + '%';
        });

        // ============ CURSOR ============
        const cursorGlow = document.getElementById('cursorGlow');
        const cursorDot = document.getElementById('cursorDot');

        document.addEventListener('mousemove', function(e) {
            cursorGlow.style.left = (e.clientX - 100) + 'px';
            cursorGlow.style.top = (e.clientY - 100) + 'px';
            cursorDot.style.left = (e.clientX - 4) + 'px';
            cursorDot.style.top = (e.clientY - 4) + 'px';
        });

        // ============ LIVE CLOCK ============
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('en-US', { hour12: false });
            document.getElementById('liveClock').textContent = timeStr;
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ============ GREETING ============
        const hour = new Date().getHours();
        let greeting = 'Good Evening';
        if (hour < 12) greeting = 'Good Morning';
        else if (hour < 18) greeting = 'Good Afternoon';
        document.getElementById('heroGreeting').textContent = '👋 ' + greeting + '!';

        // ============ TYPING EFFECT ============
        const texts = [
            'AIM-LIGHT High School',
            'A Commitment to Excellence',
            'All-in-One Administration Panel',
            'Integrated School Management',
            'Smart Boarding Solutions'
        ];
        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        const typedText = document.getElementById('typedText');

        function typeEffect() {
            const currentText = texts[textIndex];
            if (isDeleting) {
                typedText.textContent = currentText.substring(0, charIndex - 1);
                charIndex--;
            } else {
                typedText.textContent = currentText.substring(0, charIndex + 1);
                charIndex++;
            }

            if (!isDeleting && charIndex === currentText.length) {
                isDeleting = true;
                setTimeout(typeEffect, 2000);
                return;
            }

            if (isDeleting && charIndex === 0) {
                isDeleting = false;
                textIndex = (textIndex + 1) % texts.length;
            }

            setTimeout(typeEffect, isDeleting ? 50 : 100);
        }
        typeEffect();

        // ============ PARTICLE NETWORK ============
        const canvas = document.getElementById('particleCanvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const particles = [];
        const particleCount = 40;

        for (let i = 0; i < particleCount; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                vx: (Math.random() - 0.5) * 0.4,
                vy: (Math.random() - 0.5) * 0.4,
                radius: Math.random() * 2 + 1
            });
        }

        function drawParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            particles.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;

                if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
                if (p.y < 0 || p.y > canvas.height) p.vy *= -1;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(99, 102, 241, 0.4)';
                ctx.fill();
            });

            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < 120) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(99, 102, 241, ' + (1 - dist / 120) * 0.25 + ')';
                        ctx.lineWidth = 1;
                        ctx.stroke();
                    }
                }
            }

            requestAnimationFrame(drawParticles);
        }
        drawParticles();

        window.addEventListener('resize', function() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        // ============ THEME SWITCH ============
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
</body>
</html>

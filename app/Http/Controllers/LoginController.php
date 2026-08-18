<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Role-to-route mapping for redirection after login.
     * Add or remove entries as your roles change.
     */
    protected array $roleRedirects = [
        'admin'               => 'admin.dashboard',
        'head-teacher'        => 'headteacher.dashboard',
        'teacher'             => 'teacher.dashboard',
        'boarding-officer'    => 'boarding.dashboard',
        'warden-matron'       => 'boarding.dashboard',
        'bursar'              => 'finance.dashboard',
        'accountant'          => 'finance.dashboard',
        'procurement-officer' => 'procurement.dashboard',
        'auditor'             => 'auditor.dashboard',
        'student'             => 'student.dashboard',
    ];

    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $this->ensureIsNotRateLimited($request);

        if (! Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // Check if the user is allowed to log in (e.g., not suspended)
        if (! $user->canAuthenticate()) {
            Auth::logout();

            $message = $user->status === 'suspended' && $user->suspended_until
                ? 'Your account is suspended until ' . $user->suspended_until->format('d M Y')
                : 'Your account is not active. Please contact administrator.';

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        // Record login metadata (IP, user agent)
        $user->recordLogin($request->ip(), $request->userAgent());

        // Record login history entry
        LoginHistory::create([
            'user_id'    => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at'   => now(),
        ]);

        RateLimiter::clear($this->throttleKey($request));
        $request->session()->regenerate();

        return $this->redirectUser($user);
    }

    public function logout(Request $request)
    {
        $userId = Auth::id();

        // Update the latest login history record with logout time
        $latestLogin = LoginHistory::where('user_id', $userId)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($latestLogin) {
            $latestLogin->update(['logout_at' => now()]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect the authenticated user based on their role.
     * If no matching role is found, fallback to a default dashboard.
     */
    protected function redirectUser(User $user)
    {
        // Find the first role the user has that exists in our mapping
        foreach ($this->roleRedirects as $role => $route) {
            if ($user->hasRole($role)) {
                return redirect()->intended(route($route));
            }
        }

        // Fallback if no role matches (should not happen in a well-configured system)
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Ensure the login request is not rate-limited.
     * Throws validation exception if too many attempts.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        $key = $this->throttleKey($request);

        if (! RateLimiter::tooManyAttempts($key, 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate-limiting key for the request.
     * Uses email + IP for better protection.
     */
    protected function throttleKey(Request $request): string
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }
}

<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if user is active
        if (! $user->canAuthenticate()) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account is not active. Please contact the administrator.');
        }

        // If no roles are passed, allow access (should not happen, but just in case)
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (! $user->hasAnyRole($roles)) {
            abort(403, 'Unauthorized action. You do not have the required role.');
        }

        return $next($request);
    }
}
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProcurementStaffMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (! $user->hasAnyRole(['procurement-officer', 'admin'])) {
            abort(403, 'Unauthorized. Only procurement staff can access this section.');
        }

        if (! $user->staff) {
            abort(403, 'You do not have a staff profile. Please contact the administrator.');
        }

        return $next($request);
    }
}

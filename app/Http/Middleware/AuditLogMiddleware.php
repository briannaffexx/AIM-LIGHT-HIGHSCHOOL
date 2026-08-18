<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            Log::info('User Activity', [
                'user_id'   => Auth::id(),
                'user_name' => Auth::user()->full_name,
                'method'    => $request->method(),
                'path'      => $request->path(),
                'route'     => $request->route() ? $request->route()->getName() : null,
                'ip'        => $request->ip(),
                'timestamp' => now()->toDateTimeString(),
            ]);
        }

        return $response;
    }
}

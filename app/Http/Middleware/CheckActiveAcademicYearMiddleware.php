<?php
namespace App\Http\Middleware;

use App\Models\AcademicYear;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveAcademicYearMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $activeYear = AcademicYear::where('is_active', true)->first();

        if (! $activeYear) {
            return redirect()->back()
                ->with('error', 'No active academic year found. Please set up an academic year first.');
        }

        // Share the active year with the request
        $request->merge(['active_academic_year_id' => $activeYear->id]);

        return $next($request);
    }
}

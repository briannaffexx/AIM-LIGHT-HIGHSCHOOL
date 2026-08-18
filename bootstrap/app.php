<?php

use App\Http\Middleware\AuditLogMiddleware;
use App\Http\Middleware\CheckActiveAcademicYearMiddleware;
use App\Http\Middleware\CheckBoardingStaffMiddleware;
use App\Http\Middleware\CheckFinanceStaffMiddleware;
use App\Http\Middleware\CheckProcurementStaffMiddleware;
use App\Http\Middleware\CheckStaffMiddleware;
use App\Http\Middleware\CheckStudentMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'              => RoleMiddleware::class,
            'staff'             => CheckStaffMiddleware::class,
            'student'           => CheckStudentMiddleware::class,
            'boarding.staff'    => CheckBoardingStaffMiddleware::class,
            'finance.staff'     => CheckFinanceStaffMiddleware::class,
            'procurement.staff' => CheckProcurementStaffMiddleware::class,
            'active.year'       => CheckActiveAcademicYearMiddleware::class,
            'audit.log'         => AuditLogMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
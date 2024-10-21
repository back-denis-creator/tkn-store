<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->role !== User::ADMIN_NAME) {
            return Inertia::render('Welcome', [
                'canLogin' => Route::has('login'),
                'canRegister' => Route::has('register'),
                'laravelVersion' => Application::VERSION,
                'phpVersion' => PHP_VERSION,
            ]);
        }

        return $next($request);

        // not able to access through gate
        // return Inertia::render("Error", [
        //     "customError" =>
        //         "Check your current Role as your current Role does not allow you to perform this action.",
        //     "status" => 403,
        // ])
        //     ->toResponse($request)
        //     ->setStatusCode(403);
    }
}

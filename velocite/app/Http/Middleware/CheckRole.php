<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // Redirect to the appropriate dashboard based on user role
        $user = $request->user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard')
                    ->with('error', 'You do not have permission to access that area.');
            case 'partner':
                return redirect()->route('partner.dashboard')
                    ->with('error', 'You do not have permission to access that area.');
            case 'agent':
                return redirect()->route('agent.dashboard')
                    ->with('error', 'You do not have permission to access that area.');
            default:
                return redirect()->route('client.dashboard')
                    ->with('error', 'You do not have permission to access that area.');
        }
    }
}

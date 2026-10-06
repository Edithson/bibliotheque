<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $requiredLevel = match ($role) {
            'admin' => 4,
            'gerant' => 3,
            'auteur', 'author' => 2,
            default => 1,
        };

        if (! $request->user()->hasRoleLevel($requiredLevel)) {
            abort(403, 'Accès non autorisé : vous ne disposez pas des privilèges requis.');
        }

        return $next($request);
    }
}

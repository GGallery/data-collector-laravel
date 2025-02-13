<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiTokenPrefix;

class AuthenticateWithPrefixToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $prefix_token = $request->bearerToken();
        // dd($prefixToken);

        if (!$prefix_token || !ApiTokenPrefix::where('prefix_token', $prefix_token)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Passa la richiesta al prossimo middleware o controller
        $response = $next($request);

        // Aggiunge un header personalizzato alla risposta
        $response->headers->set('Authorization', 'Bearer ' . $prefix_token);

        return $response;
    }
}

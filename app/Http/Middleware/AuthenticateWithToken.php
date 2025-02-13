<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiTokenPrefix;
use Illuminate\Support\Facades\Crypt;

class AuthenticateWithToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $encrypted_token = $request->bearerToken();
        
        // Controlla se il token è presente
        if (!$encrypted_token) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Decripta il token
        try {
            $composed_token = Crypt::decrypt($encrypted_token);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Estrae il prefisso dal token
        $prefix_token = substr($composed_token, 0, 10);
        $dynamic_part = substr($composed_token, 10);

        // Verifica se il prefisso esiste nel database
        $api_token_prefix = ApiTokenPrefix::where('prefix_token', $prefix_token)->first();
        if (!$api_token_prefix) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Verifica la parte dinamica del token
        $expected_dynamic_part = $api_token_prefix->created_at->timestamp;
        if ($dynamic_part != $expected_dynamic_part) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Passa la richiesta al prossimo middleware o controller
        $response = $next($request);
        $response->headers->set('Authorization', 'Bearer ' . $composed_token);
        // dd($response);

        // Aggiunge un header personalizzato alla risposta
        // $response->headers->set('Authorization', 'Bearer ' . $composed_token);

        return $response;
    }
}
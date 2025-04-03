<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiTokenPrefix;
use App\Models\SystemLog;
use Exception;
// use Illuminate\Support\Facades\Crypt;
use App\Traits\LogErrorTrait;
use App\Helpers\EncryptionHelper;


require_once app_path('Helpers/EncryptionHelper.php');

class AuthenticateWithToken
{

    use LogErrorTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $encrypted_token = $request->bearerToken(); //si aspetta il token completo
        
        // Controlla se il token è presente
        if (!$encrypted_token) {
            $this->logError(__FILE__, 'AuthenticateWithToken', 'Token non fornito');
            return response()->json(['message' => 'Token non fornito'], 401);
        }

        // Decripta il token
        $secret_key = env('SECRET_KEY');
        $secret_iv = env('SECRET_IV');
        // dd($secret_key, $secret_iv);
        try {
            $composed_token = EncryptionHelper::encryptDecrypt($encrypted_token, $secret_key, $secret_iv, 'decrypt');
        } catch (Exception $e) {
            $this->logError(__FILE__, 'AuthenticateWithToken', 'Token decryption fallita: ' . $e->getMessage());
            return response()->json(['message' => 'Token decryption fallita'], 401);
        }

        // Estrae il prefisso dal token ricevuto
        $prefix_token = substr($composed_token, 0, 10);
        // Estrae la parte dinamica (timestamp) dal token ricevuto
        $dynamic_part = substr($composed_token, 10);
        // dd($dynamic_part);

        // Verifica se il prefisso esiste nel database
        $api_token_prefix = ApiTokenPrefix::where('prefix_token', $prefix_token)->first();
        if (!$api_token_prefix) {
            $this->logError(__FILE__, 'AuthenticateWithToken', 'Prefix token not found');
            return response()->json(['message' => 'Prefix token not found'], 401);
        }

        // Aggiunge platform_prefix alla richiesta
        $request->merge(['platform_prefix' => $prefix_token]);        

        // Ottiene il timestamp di creazione del token dal database
        $expected_dynamic_part = $api_token_prefix->created_at->timestamp;
        $time_window = 604800; // 1 settimana di validità solo per test

        // Confronta la differenza tra il timestamp del token ricevuto e quello salvato nel database
        if (abs($dynamic_part - $expected_dynamic_part) > $time_window) {
            $this->logError(__FILE__, 'AuthenticateWithToken', 'Token expired or invalid');            
            return response()->json(['message' => 'Token expired or invalid'], 401);
        }

        // Passa la richiesta al prossimo middleware o controller
        $response = $next($request);
        // $response->headers->set('Authorization', 'Bearer ' . $composed_token);
        // dd($response);

        return $response;
    }


}
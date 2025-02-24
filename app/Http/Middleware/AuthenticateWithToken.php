<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiTokenPrefix;
use App\Models\SystemLog;
use Exception;
// use Illuminate\Support\Facades\Crypt;

require_once app_path('Helpers/EncryptionHelper.php');

class AuthenticateWithToken
{
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
            $composed_token = \App\Helpers\EncryptionHelper::encryptDecrypt($encrypted_token, $secret_key, $secret_iv, 'decrypt');
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

    protected function logError($file, $function_name, $message)
    {
        $platform_name = 'Unknown'; // Valore default
        $debug_info = [];
        //forse da cancellare

        // Recupera platform_name da api_tokens_prefixes, se disponibile
        $bearer_token = request()->bearerToken();
        $debug_info['bearer_token'] = $bearer_token;

        if ($bearer_token) {
            try {
                $decrypted_token = \App\Helpers\EncryptionHelper::encryptDecrypt($bearer_token, env('SECRET_KEY'), env('SECRET_IV'), 'decrypt');
                $debug_info['decrypted_token'] = $decrypted_token;
                
                $prefix_token = substr($decrypted_token, 0, 10);
                $debug_info['prefix_token'] = $prefix_token;
                
                $api_token_prefix = ApiTokenPrefix::where('prefix_token', $prefix_token)->first();
                $debug_info['api_token_prefix'] = $api_token_prefix;
                
                if ($api_token_prefix) {
                    $platform_name = $api_token_prefix->platform_name;
                }
            } catch (Exception $e) {
                $debug_info['error'] = $e->getMessage();
            }
        }

        // Aggiungi le informazioni di debug al messaggio
        $debug_message = $message . "\nDebug Info: " . json_encode($debug_info);
        
        SystemLog::create([
            'file' => $file,
            'platform_name' => $platform_name,
            'function_name' => $function_name,
            'message' => $debug_message,
        ]);
    }

}
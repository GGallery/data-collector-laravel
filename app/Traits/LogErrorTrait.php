<?php

namespace App\Traits;

use App\Models\SystemLog;
use App\Models\ApiTokenPrefix;
use Exception;

//Funzione custom per i log errors con l'identificazione della piattaforma
trait LogErrorTrait 
{
    protected function logError($file, $function_name, $message, $request = null)
    {
        // Recupera platform_name da api_tokens_prefixes, se disponibile
        $platform_name = 'Unknown';
        $debug_info = [];

        // Prende il bearer token da request per identificazione della piattaforma
        $bearer_token = $request ? $request->bearerToken() : request()->bearerToken();

        if ($bearer_token) {
            try {
                // Decritta il token
                $decrypted_token = \App\Helpers\EncryptionHelper::encryptDecrypt($bearer_token, env('SECRET_KEY'), env('SECRET_IV'), 'decrypt');

                // Dal token estrae il prefisso
                $prefix_token = substr($decrypted_token, 0, 10);
                
                // Con il prefisso estratto cerca il record corrispondente sul db
                $api_token_prefix = ApiTokenPrefix::where('prefix_token', $prefix_token)->first();
                
                if ($api_token_prefix) {
                    // Se lo trova viene associato alla variabile
                    $platform_name = $api_token_prefix->platform_name;
                }
            } catch (Exception $e) {
                $debug_info['token_error'] = $e->getMessage();
            }
        }

        // Recupera o inizializza l'array degli errori dalla sessione
        $errors = session('log_errors', []);

        // Aggiunge il nuovo errore
        $errors[] = [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'type' => 'server',
            'context' => [
                'file' => $file,
                'function' => $function_name,
                'email' => $request ? $request->input('email') : null
            ],
            'error' => [
                'message' => $message,
                'debug_info' => $debug_info,
                'trace' => array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 5)
            ]
        ];

        // Salva l'array aggiornato nella sessione
        session(['log_errors' => $errors]);

        // Crea un record nel db con le informazioni di log
        SystemLog::create([
            'file' => $file,
            'platform_name' => $platform_name,
            'function_name' => $function_name,
            'message' => json_encode(['errors' => $errors], JSON_PRETTY_PRINT),
            'email' => $request ? $request->input('email') : null
        ]);
    }
}
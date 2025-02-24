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
        // Array delle informazioni di debug
        $debug_info['bearer_token'] = $bearer_token;

        if ($bearer_token) {
            try {
                // Decritta il token
                $decrypted_token = \App\Helpers\EncryptionHelper::encryptDecrypt($bearer_token, env('SECRET_KEY'), env('SECRET_IV'), 'decrypt');
                $debug_info['decrypted_token'] = $decrypted_token;

                // Dal token estrae il prefisso
                $prefix_token = substr($decrypted_token, 0, 10);
                $debug_info['prefix_token'] = $prefix_token;
                
                // Con il prefisso estratto cerca il record corrispondente sul db
                $api_token_prefix = ApiTokenPrefix::where('prefix_token', $prefix_token)->first();
                $debug_info['api_token_prefix'] = $api_token_prefix;
                
                if ($api_token_prefix) {
                    // Se lo trova viene associato alla variabile
                    $platform_name = $api_token_prefix->platform_name;
                }
            } catch (Exception $e) {
                $debug_info['error'] = $e->getMessage();
            }
        }

        // Aggiunge le informazioni di debug al messaggio
        $debug_message = $message . "\nDebug Info: " . json_encode($debug_info);

        // Crea un record nel db con le informazioni di log
        SystemLog::create([
            'file' => $file,
            'platform_name' => $platform_name,
            'function_name' => $function_name,
            'message' => $debug_message,
            'email' => $request ? $request->input('email') : null
        ]);
    }
}
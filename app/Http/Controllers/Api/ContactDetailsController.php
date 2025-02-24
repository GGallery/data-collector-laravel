<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactDetailsResource;
use App\Models\ApiTokenPrefix;
use App\Models\ContactDetails;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;
use App\Models\SystemLog;
use Exception;



class ContactDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ContactDetailsResource::collection(ContactDetails::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $contactDetails = ContactDetails::create($request->all());
            return new ContactDetailsResource($contactDetails);
        } catch (Exception $e) {
            // Chiama la funzione logError
            $this->logError(__FILE__, __FUNCTION__, $e->getMessage(), $request);
            return response()->json([
                'message' => 'Error storing contact details',
                'debug_info' => $e->getMessage(),
                // 'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    //Funzione custom per i log errors con l'identificazione della piattaforma
    protected function logError($file, $function_name, $message, $request)
    {
        // Recupera platform_name da api_tokens_prefixes, se disponibile
        $platform_name = 'Unknown'; // Valore default
        $debug_info = [];

        // Prende il bearer token da request per identificazione della piattaforma
        $bearer_token = $request->bearerToken();
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
                
                // Con il prefisso estratto cerca il record corrispondente il db
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
            'email' => $request->input('email'),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contactDetails = ContactDetails::find($id);

        if (!$contactDetails) {
            return response()->json(['message' => 'Contact details not found'], 404);
        }

        return new ContactDetailsResource($contactDetails);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contactDetails = ContactDetails::find($id);

        if (!$contactDetails) {
            return response()->json(['message' => 'Contact details not found'], 404);
        }

        $contactDetails->update($request->all());
        return new ContactDetailsResource($contactDetails);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contactDetails = ContactDetails::find($id);

        if (!$contactDetails) {
            return response()->json(['message' => 'Contact details not found'], 404);
        }

        $contactDetails->delete();
        return response()->json(null, 204);
    }
}

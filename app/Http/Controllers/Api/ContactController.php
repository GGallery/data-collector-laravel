<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\ApiTokenPrefix;
use App\Models\Contact;
use App\Models\SystemLog;
use Exception;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ContactResource::collection(Contact::all());
    }

    // 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $contact = Contact::create($request->all());
            return new ContactResource($contact);
        } catch (Exception $e) {
            $this->logError(__FILE__, __FUNCTION__, $e->getMessage(), $request);
            return response()->json([
                'message' => 'Error storing contact',
                'debug_info' => $e->getMessage()
            ], 500);

        }
    }

    protected function logError($file, $function_name, $message, $request)
    {
        $platform_name = 'Unknown';
        $debug_info = [];

        $bearer_token = $request->bearerToken();
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

        $debug_message = $message . "\nDebug Info: " . json_encode($debug_info);

        SystemLog::create([
            'file' => $file,
            'platform_name' => $platform_name,
            'function_name' => $function_name,
            'message' => $debug_message,
            'email' => $request->input('email')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        return new ContactResource($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        $contact->update($request->all());
        return new ContactResource($contact);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contact = Contact::find($id);

        if (!$contact) {
            return response()->json(['message' => 'Contact not found'], 404);
        }

        $contact->delete();
        return response()->json(null, 204);
    }


}

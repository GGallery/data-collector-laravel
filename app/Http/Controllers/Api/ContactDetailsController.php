<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactDetailsResource;
use App\Models\ApiTokenPrefix;
use App\Models\Contact;
use App\Models\ContactDetails;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;
use App\Models\SystemLog;
use Exception;
use App\Traits\LogErrorTrait;



class ContactDetailsController extends Controller
{

    use LogErrorTrait;


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

            // Debug
            // dd($request);

            // Cerca il contatto in tabella `contacts` associato usando l'email e platform_prefix
            $contact = Contact::where('email', $request->input('email'))
                                        ->where('platform_prefix', $request->input('platform_prefix'))
                                        ->first();
            
            if (!$contact) {
                return response()->json([
                    'message' => 'Contact not found for the provided email and platform',
                    'email' => $request->input('email'),
                    'platform_prefix' => $request->input('platform_prefix')
                ], 404);
            }
            
            // Prepara i dati per i dettagli del contatto
            $data = $request->except('email');
            $data['contact_id'] = $contact->id;

            $contactDetails = ContactDetails::create($data);
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
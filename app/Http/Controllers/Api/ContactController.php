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
use App\Traits\LogErrorTrait;
use Illuminate\Support\Facades\DB;


class ContactController extends Controller
{
    use LogErrorTrait;

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
            // Controlla se è un batch di contatti
            if ($request->has('contacts') && is_array($request->contacts)) {
                return $this->storeBatch($request);
            }
            
            
            // Elaborazione di un singolo contatto
            $contact = Contact::create($request->all());
            return new ContactResource($contact);
        } catch (Exception $e) {
            // Log dell'errore fuori dalla transazione
            $this->logError(__FILE__, __FUNCTION__, $e->getMessage(), $request);
            
            return response()->json([
                'message' => 'Error storing contact',
                'debug_info' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store contatti multipli in batch.
     */
    protected function storeBatch(Request $request)
    {
        $insertedContacts = [];
        
        try {
            // Inizia la transazione
            DB::beginTransaction();
            
            foreach ($request->contacts as $contactData) {
                // Cerca se il contatto esiste già
                $contact = Contact::firstOrNew(['email' => $contactData['email']]);
                
                if (!$contact->exists) {
                    // Imposta solo i campi che sono forniti
                    $contact->fill(array_filter($contactData));
                    $contact->save();
                }
                
                $insertedContacts[] = $contact;
            }
            
            // Commit della transazione
            DB::commit();
            
            return ContactResource::collection(collect($insertedContacts));
        } catch (Exception $e) {
            // Rollback della transazione
            DB::rollBack();
            
            // Log dell'errore fuori dalla transazione
            $this->logError(__FILE__, 'storeBatch', $e->getMessage(), $request);
            
            return response()->json([
                'message' => 'Error storing contacts batch',
                'debug_info' => $e->getMessage()
            ], 500);
        }
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
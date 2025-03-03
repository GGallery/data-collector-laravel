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
use App\Models\Contact;
use App\Traits\LogErrorTrait;
use Illuminate\Support\Facades\DB;




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
             // Controlla se è un batch di dettagli
             if ($request->has('contacts_details') && is_array($request->contacts_details)) {
                 return $this->storeBatch($request);
             }
             
             // Trova il contatto associato usando l'email
             $email = $request->input('email');
             $contact = Contact::where('email', $email)->first();
             
             if (!$contact) {
                 return response()->json([
                     'message' => "Contact with email {$email} not found"
                 ], 404);
             }
             
             // Crea i dettagli per il contatto
             $contactDetails = new ContactDetails();
             $contactDetails->contact_id = $contact->id;
             
             // Riempi i campi con i dati forniti o valori di default per campi richiesti
             $data = $request->all();
             unset($data['email']); // Rimuovi email perché non è nella tabella contacts_details
             
             // Assicurati che i campi obbligatori abbiano valori, anche vuoti
             $requiredFields = [
                 'cb_cognome', 'cb_codicefiscale', 'cb_datadinascita', 'cb_luogodinascita', 
                 'cb_provinciadinascita', 'cb_indirizzodiresidenza', 'cb_telefono', 'cb_nome', 
                 'cb_citta', 'cb_profiloprofessionale', 'cb_societa'
             ];
             
             foreach ($requiredFields as $field) {
                 if (!isset($data[$field])) {
                     $data[$field] = '';
                 }
             }
             
             $contactDetails->fill($data);
             $contactDetails->save();
             
             return new ContactDetailsResource($contactDetails);
         } catch (Exception $e) {
             // Log dell'errore
             $this->logError(__FILE__, __FUNCTION__, $e->getMessage(), $request);
             
             return response()->json([
                 'message' => 'Error storing contact details',
                 'debug_info' => $e->getMessage()
             ], 500);
         }
     }


     protected function storeBatch(Request $request)
     {
         $insertedDetails = [];
         
         try {
             // Inizia la transazione
             DB::beginTransaction();
             
             foreach ($request->contacts_details as $detailsData) {
                 $email = $detailsData['email'] ?? null;
                 
                 if (!$email) {
                     continue; // Salta record senza email
                 }
                 
                 // Trova il contatto associato
                 $contact = Contact::where('email', $email)->first();
                 
                 if (!$contact) {
                     // Log di avviso ma continua con altri record
                     $this->logError(__FILE__, 'storeBatch', "Contact with email {$email} not found", $request);
                     continue;
                 }
                 
                 // Crea o aggiorna i dettagli
                 $contactDetails = ContactDetails::firstOrNew(['contact_id' => $contact->id]);
                 
                 // Copia dei dati, esclusa l'email
                 $data = $detailsData;
                 unset($data['email']);
                 
                 // Assicurati che i campi obbligatori abbiano valori, anche vuoti
                 $requiredFields = [
                     'cb_cognome', 'cb_codicefiscale', 'cb_datadinascita', 'cb_luogodinascita', 
                     'cb_provinciadinascita', 'cb_indirizzodiresidenza', 'cb_telefono', 'cb_nome', 
                     'cb_citta', 'cb_profiloprofessionale', 'cb_societa'
                 ];
                 
                 foreach ($requiredFields as $field) {
                     if (!isset($data[$field])) {
                         $data[$field] = '';
                     }
                 }
                 
                 $contactDetails->fill($data);
                 $contactDetails->save();
                 
                 $insertedDetails[] = $contactDetails;
             }
             
             // Commit della transazione
             DB::commit();
             
             return ContactDetailsResource::collection(collect($insertedDetails));
         } catch (Exception $e) {
             // Rollback della transazione
             DB::rollBack();
             
             // Log dell'errore fuori dalla transazione
             $this->logError(__FILE__, 'storeBatch', $e->getMessage(), $request);
             
             return response()->json([
                 'message' => 'Error storing contact details batch',
                 'debug_info' => $e->getMessage()
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
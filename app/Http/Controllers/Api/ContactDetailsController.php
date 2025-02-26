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
            // Utilizza DB::raw per filtrare e ordinare i dettagli dei contatti
            $contactDetails = DB::table('contact_details')
                ->select(DB::raw('*'))
                ->whereBetween('lastupdatedate', [$request->startDate, $request->endDate])
                ->orderBy('lastupdatedate', 'desc')
                ->get();

            foreach ($contactDetails as $details) {
                ContactDetails::create((array) $details);
            }

            return response()->json(['message' => 'Contact details stored successfully'], 200);
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

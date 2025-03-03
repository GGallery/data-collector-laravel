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
            $contact = Contact::create($request->all());
            return new ContactResource($contact);
        } catch (Exception $e) {
            // $this->logError(__FILE__, __FUNCTION__, $e->getMessage(), $request);
            return response()->json([
                'message' => 'Error storing contact',
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
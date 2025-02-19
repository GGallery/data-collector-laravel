<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactExtraResource;
use App\Models\ContactExtra;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;


class ContactExtraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ContactExtraResource::collection(ContactExtra::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $contactExtra = ContactExtra::create($request->all());
        return new ContactExtraResource($contactExtra);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contactExtra = ContactExtra::find($id);

        if (!$contactExtra) {
            return response()->json(['message' => 'Contact extra not found'], 404);
        }

        return new ContactExtraResource($contactExtra);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $contactExtra = ContactExtra::find($id);

        if (!$contactExtra) {
            return response()->json(['message' => 'Contact extra not found'], 404);
        }

        $contactExtra->update($request->all());
        return new ContactExtraResource($contactExtra);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $contactExtra = ContactExtra::find($id);

        if (!$contactExtra) {
            return response()->json(['message' => 'Contact extra not found'], 404);
        }

        $contactExtra->delete();
        return response()->json(null, 204);
    }
}

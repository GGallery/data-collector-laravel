<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SystemLogResource;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Exception;

class SystemLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $logs = SystemLog::latest()->paginate(20);
            return SystemLogResource::collection($logs);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Errore nel recupero dei log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validazione di base
            $validated = $request->validate([
                'file' => 'required|string',
                'function_name' => 'required|string',
                'message' => 'required|string',
                'platform_name' => 'required|string',
                'email' => 'nullable|string'
            ]);
            
            // Creazione del log
            $log = SystemLog::create([
                'file' => $validated['file'],
                'function_name' => $validated['function_name'],
                'message' => $validated['message'],
                'platform_name' => $validated['platform_name']
            ]);
            
            return new SystemLogResource($log);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Errore nella creazione del log',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $log = SystemLog::findOrFail($id);
            return new SystemLogResource($log);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Log non trovato',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Per i log, di solito non si permette l'aggiornamento
        return response()->json([
            'message' => 'Aggiornamento dei log non consentito'
        ], 403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $log = SystemLog::findOrFail($id);
            $log->delete();
            
            return response()->json([
                'message' => 'Log eliminato con successo'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Errore nell\'eliminazione del log',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
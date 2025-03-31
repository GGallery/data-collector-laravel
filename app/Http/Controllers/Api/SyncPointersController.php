<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SyncPointersResource;
use App\Models\SyncPointer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncPointersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Trova o crea il record di sincronizzazione per questa piattaforma
            $syncPointer = SyncPointer::updateOrCreate(
                ['platform_prefix' => $request->platform_prefix],
                [
                    'last_id_processed' => $request->last_id_processed,
                    'last_sync_date' => $request->last_sync_date,
                ]
            );
            
            // Aggiorna le statistiche (se fornite)
            if ($request->has('processed_records')) {
                $syncPointer->processed_records = ($syncPointer->processed_records ?? 0) + $request->processed_records;
            }
            
            if ($request->has('success_count')) {
                $syncPointer->success_count = ($syncPointer->success_count ?? 0) + $request->success_count;
            }
            
            if ($request->has('error_count')) {
                $syncPointer->error_count = ($syncPointer->error_count ?? 0) + $request->error_count;
            }
            
            // Salva le modifiche
            $syncPointer->save();
            
            // Log dell'aggiornamento
            Log::info('Sync pointer updated', [
                'platform' => $request->platform_prefix,
                'last_id' => $request->last_id_processed,
                'last_sync' => $request->last_sync_date
            ]);
            
            return new SyncPointersResource($syncPointer);

        } catch (Exception $e) {
            Log::error('Error updating sync pointer', [
                'platform' => $request->platform_prefix,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating sync pointer',
                'error' => $e->getMessage()
            ], 500);
        }        

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SyncPointersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'platform_prefix' => $this->platform_prefix,
            'platform_name' => $this->whenLoaded('platform', function() {
                return $this->platform->platform_name;
            }),
            'last_id_processed' => $this->last_id_processed,
            'last_sync_date' => $this->last_sync_date,
            'processed_records' => $this->processed_records,
            'success_count' => $this->success_count,
            'error_count' => $this->error_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}

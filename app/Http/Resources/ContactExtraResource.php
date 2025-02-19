<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactExtraResource extends JsonResource
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
            'cb_cognome' => $this->cb_cognome,
            'cb_codicefiscale' => $this->cb_codicefiscale,
            'cb_datadinascita' => $this->cb_datadinascita,
            'cb_luogodinascita' => $this->cb_luogodinascita,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

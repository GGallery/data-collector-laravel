<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactDetailsResource extends JsonResource
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
            'cb_provinciadinascita' => $this->cb_provinciadinascita, 
            'cb_indirizzodiresidenza' => $this->cb_indirizzodiresidenza,
            'cb_provdiresidenza' => $this->cb_provdiresidenza,
            'cb_cap' => $this->cb_cap,
            'cb_telefono'=> $this->cb_telefono,
            'cb_nome' => $this->cb_nome,
            'cb_citta' => $this->cb_citta,
            'cb_professionedisciplina' => $this->cb_professionedisciplina, 
            'cb_ordine' => $this->cb_ordine,
            'cb_numeroiscrizione' => $this->cb_numeroiscrizione,
            'cb_reclutamento' => $this->cb_reclutamento,
            'cb_codicereclutamento' => $this->cb_codicereclutamento, 
            'cb_professione' => $this->cb_professione,
            'cb_profiloprofessionale' => $this->cb_profiloprofessionale,
            'cb_settore' => $this->cb_settore,
            'cb_societa' => $this->cb_societa,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

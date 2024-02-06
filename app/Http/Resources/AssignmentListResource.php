<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        $id_ppk = $this->ppk;
        $head_officer = $this->head_officer;

        return [
            "id"=> $this->id,
            "user_id"=> $this->user_id,
            "ppk"=> $this->nama_ppk,
            "head_officer"=> $head_officer !== 0 ? $head_officer : null,
            "unit"=> $this->unit,
            "ndreq_st"=> $this->ndreq_st,
            "no_st"=> $this->no_st,
            "nomor_st"=> $this->nomor_st,
            "date_st"=> $this->date_st,
            "no_spd"=> $this->no_spd,
            "date_spd"=> $this->date_spd,
            "departure_date"=> $this->departure_date,
            "return_date"=> $this->return_date,
            "dipa_search"=> $this->dipa_search,
            "tagging_status"=> $this->tagging_status,
            "plt"=> $this->plt,
            "plh"=> $this->plh,
            "disbursement"=> $this->disbursemenet,
            "no_spyt"=> $this->no_spyt,
            "implementation_tasks"=> $this->implementation_tasks,
            "business_trip_reason"=> $this->business_trip_reason,
            "destination_office"=> $this->destination_office,
            "city_origin"=> $this->city_origin,
            "destination_city_1"=> $this->destination_city_1,
            "destination_city_2"=> $this->destination_city_2,
            "destination_city_3"=> $this->destination_city_3,
            "destination_city_4"=> $this->destination_city_4,
            "destination_city_5"=> $this->destination_city_5,
            "transportation"=> $this->transportation,
            "signature"=> $this->signature,
            "employee_status"=> $this->employee_status,
            "availability_status"=> $this->availability_status,
            "ppk_status"=> $this->ppk_status,
            "head_officer_status"=> $this->head_officer_status,
            "jabPeg"=> $this->jabPeg,
            "pangkatPeg"=> $this->pangkatPeg,
            "golPeg"=> $this->golPeg,
            "nip_peg"=> $this->nip_peg,
            "nip_ppk"=> $this->nip_ppk,
            "employee"=> $this->employee,
            "nama_pej"=> $this->nama_pej,
            "nama_ppk"=> $this->nama_ppk,
            "nomor_identitas"=> $this->nomor_identitas,
            "id_ppk"=> $id_ppk !== 0 ? $id_ppk : null,
        ];
    }
}

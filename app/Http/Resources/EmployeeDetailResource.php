<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'emp_id'=>$this->emp_id,
            'rank'=>$this->rank,
            'gol_room'=>$this->gol_room,
            'position'=>$this->position,
            'role'=>$this->getRoleNames()->first()
        ];
    }
}

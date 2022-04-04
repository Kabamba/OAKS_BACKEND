<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MinistryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "libelle" => $this->libelle,   
            "full_desc" => $this->descriptions,
            "small_desc" => substr($this->descriptions,0,90).' ...',
            "very_small_desc" => substr($this->descriptions,0,40).' ...',
            "leader_name" => $this->leader_name,
            "image" => $this->image
        ];
    }
}

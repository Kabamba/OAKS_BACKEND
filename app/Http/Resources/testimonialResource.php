<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class testimonialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $cover = "";

        foreach ($this->images as $image) {
            if ($image->covert == 1) {
                $cover = $image->chemin;
            }
        }

        return [
            'id' => $this->id,
            'titre' => $this->titre,
            "full_desc" => $this->descriptions,
            "small_desc" => substr($this->descriptions,0,90).' ...',
            "very_small_desc" => substr($this->descriptions,0,40).' ...',
            'witness_name' => $this->witness_name,
            'date_testi' => date('M d,Y H:i',strtotime($this->date_testi)),
            'user_posted' => $this->user->name ?? 'Unknown',
            'is_active' => $this->is_active,
            "cover" => $cover,
            'images' => $this->images
        ];
    }
}

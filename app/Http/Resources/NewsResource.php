<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            if ($image->couvert == 1) {
                $cover = $image->chemin;
            }
        }

        return [
            'id' => $this->id,
            'titre' => $this->titre,
            "full_desc" => $this->descriptions,
            "small_desc" => substr($this->descriptions,0,90).' ...',
            "very_small_desc" => substr($this->descriptions,0,40).' ...',
            'date_event' => date('M d,Y H:i',strtotime($this->date_event)),
            'date' =>$this->date_event,
            'user_posted' => $this->user->name,
            'is_active' => $this->is_active,
            'cover' => $cover,
            'images' => $this->images
        ];
    }
}

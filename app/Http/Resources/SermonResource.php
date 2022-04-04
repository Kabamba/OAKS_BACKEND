<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SermonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            "id" => $this->id,
            "titre" => $this->titre,
            "url" => $this->url,
            "full_desc" => $this->descriptions,
            "small_desc" => substr($this->descriptions,0,80).' ...',
            "very_small_desc" => substr($this->descriptions,0,40).' ...',
            "preacher_name" => $this->preacher_name,
            "image" => $this->image,
            "date_sermon" => date('M d,Y H:i',strtotime($this->date_sermon)),
            "is_active" => $this->is_active,
            "user_posted" => $this->admin->name ?? 'Unknown',
        ];
    }
}

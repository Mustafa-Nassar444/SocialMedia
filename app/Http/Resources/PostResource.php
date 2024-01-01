<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name'=>$this->user->profile->full_name,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'photos' => $this->photos,
            'likes'=>$this->likes,
            'comments'=>$this->comments,
            // Include any other data you want to expose
        ];
    }
}

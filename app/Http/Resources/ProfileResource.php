<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'email'=>$this->user->email,
            'full_name' => $this->full_name, // If you have a full_name accessor
            'birthday' => $this->birthday,
            'gender'=>$this->gender,
            'profile_picture' => $this->profile_picture,
            'bio'=>$this->bio,
            'contact_details'=>$this->contact_details,
            'posts'=>$this->user->posts->load('photos','likes','comments'),
        ];
    }
}

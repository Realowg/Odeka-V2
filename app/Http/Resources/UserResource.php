<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar ? asset('avatar/' . $this->avatar) : null,
            'cover' => $this->cover ? asset('cover/' . $this->cover) : null,
            'verified_id' => $this->verified_id,
            'status' => $this->status,
            'bio' => $this->story,
            'location' => $this->countries_id,
            'website' => $this->website,
            'balance' => $this->balance,
            'wallet' => $this->wallet,
            'hide_profile' => $this->hide_profile === 'yes',
            'hide_last_seen' => $this->hide_last_seen === 'yes',
            'hide_count_subscribers' => $this->hide_count_subscribers === 'yes',
            'free_subscription' => $this->free_subscription === 'yes',
            'dark_mode' => $this->dark_mode,
            'language' => $this->language,
            'created_at' => $this->date,
            'last_seen' => $this->last_seen,
        ];
    }
}


<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
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
            'avatar' => $this->avatar ? asset('avatar/' . $this->avatar) : null,
            'cover' => $this->cover ? asset('cover/' . $this->cover) : null,
            'verified_id' => $this->verified_id,
            'bio' => $this->story,
            'location' => $this->city,
            'website' => $this->website,
            'profession' => $this->profession,
            'social_links' => [
                'facebook' => $this->facebook,
                'twitter' => $this->twitter,
                'instagram' => $this->instagram,
                'youtube' => $this->youtube,
                'tiktok' => $this->tiktok,
                'pinterest' => $this->pinterest,
            ],
            'subscription_price' => (float) $this->price,
            'free_subscription' => $this->free_subscription === 'yes',
            'hide_profile' => $this->hide_profile === 'yes',
            'hide_last_seen' => $this->hide_last_seen === 'yes',
            'hide_count_subscribers' => $this->hide_count_subscribers === 'yes',
            'created_at' => $this->date,
            'last_seen' => $this->last_seen,
        ];
    }
}


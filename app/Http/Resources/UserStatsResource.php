<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserStatsResource extends JsonResource
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
            'user_id' => $this->id,
            'username' => $this->username,
            'total_posts' => $this->posts()->count(),
            'total_subscribers' => $this->totalSubscriptionsActive(),
            'total_following' => $this->followingCount(),
            'total_likes' => $this->likes()->count(),
            'total_earnings' => (float) $this->balance,
            'joined_at' => $this->date,
        ];
    }
}


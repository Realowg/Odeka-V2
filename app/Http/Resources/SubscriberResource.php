<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriberResource extends JsonResource
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
            'id' => $this->subscriber->id,
            'username' => $this->subscriber->username,
            'name' => $this->subscriber->name,
            'avatar' => $this->subscriber->avatar ? asset('avatar/' . $this->subscriber->avatar) : null,
            'subscription' => [
                'id' => $this->id,
                'plan' => $this->stripe_price,
                'status' => $this->stripe_status,
                'subscribed_at' => $this->created_at?->toIso8601String(),
                'ends_at' => $this->ends_at?->toIso8601String(),
            ],
        ];
    }
}


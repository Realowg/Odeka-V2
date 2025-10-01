<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'subscriber' => $this->when($this->subscriber, function () {
                return [
                    'id' => $this->subscriber->id,
                    'username' => $this->subscriber->username,
                    'name' => $this->subscriber->name,
                    'avatar' => $this->subscriber->avatar ? asset('avatar/' . $this->subscriber->avatar) : null,
                ];
            }),
            'creator' => $this->when($this->subscribed, function () {
                return [
                    'id' => $this->subscribed->id,
                    'username' => $this->subscribed->username,
                    'name' => $this->subscribed->name,
                    'avatar' => $this->subscribed->avatar ? asset('avatar/' . $this->subscribed->avatar) : null,
                    'price' => (float) $this->subscribed->price,
                ];
            }),
            'plan_name' => $this->name,
            'plan_interval' => $this->stripe_price,
            'status' => $this->stripe_status,
            'is_active' => $this->stripe_status === 'active',
            'trial_ends_at' => $this->trial_ends_at?->toIso8601String(),
            'ends_at' => $this->ends_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}

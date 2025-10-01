<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $otherUser = $this->from_user_id == auth()->id() ? $this->receiver : $this->sender;

        return [
            'id' => $this->id,
            'user' => [
                'id' => $otherUser->id,
                'username' => $otherUser->username,
                'name' => $otherUser->name,
                'avatar' => $otherUser->avatar ? asset('avatar/' . $otherUser->avatar) : null,
                'verified' => $otherUser->verified_id === 'yes',
                'online' => $otherUser->active_status_online === 'yes',
            ],
            'last_message' => [
                'message' => $this->message,
                'from_me' => $this->from_user_id == auth()->id(),
                'is_read' => $this->status === 'readed',
                'created_at' => $this->created_at?->toIso8601String(),
            ],
            'unread_count' => $this->totalMsg(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}


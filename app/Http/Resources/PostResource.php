<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'creator' => [
                'id' => $this->creator->id,
                'username' => $this->creator->username,
                'name' => $this->creator->name,
                'avatar' => $this->creator->avatar ? asset('avatar/' . $this->creator->avatar) : null,
                'verified' => $this->creator->verified_id === 'yes',
            ],
            'media' => $this->when($this->media, function () {
                return $this->media->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => $item->type,
                        'image' => $item->image ? asset('public/images/' . $item->image) : null,
                        'video' => $item->video ? asset('public/videos/' . $item->video) : null,
                        'video_poster' => $item->video_poster ? asset('public/videos/posters/' . $item->video_poster) : null,
                    ];
                });
            }),
            'locked' => $this->locked === 'yes',
            'price' => (float) $this->price,
            'likes_count' => $this->likes ? $this->likes->count() : 0,
            'comments_count' => $this->comments ? $this->comments->count() : 0,
            'video_views' => (int) $this->video_views,
            'created_at' => $this->date,
            'scheduled_date' => $this->scheduled_date,
        ];
    }
}

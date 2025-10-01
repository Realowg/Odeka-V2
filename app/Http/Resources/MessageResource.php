<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
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
            'conversation_id' => $this->conversations_id,
            'from_user' => [
                'id' => $this->sender->id,
                'username' => $this->sender->username,
                'name' => $this->sender->name,
                'avatar' => $this->sender->avatar ? asset('avatar/' . $this->sender->avatar) : null,
            ],
            'to_user' => [
                'id' => $this->receiver->id,
                'username' => $this->receiver->username,
                'name' => $this->receiver->name,
                'avatar' => $this->receiver->avatar ? asset('avatar/' . $this->receiver->avatar) : null,
            ],
            'message' => $this->message,
            'media' => $this->when($this->media, function () {
                return $this->media->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => $item->type,
                        'url' => $this->getMediaUrl($item),
                        'thumbnail' => $this->getMediaThumbnail($item),
                    ];
                });
            }),
            'price' => (float) $this->price,
            'tip' => $this->tip === 'yes',
            'tip_amount' => (float) $this->tip_amount,
            'gift_id' => $this->gift_id,
            'gift_amount' => (float) $this->gift_amount,
            'status' => $this->status,
            'is_read' => $this->status === 'readed',
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Get media URL
     */
    private function getMediaUrl($media)
    {
        if ($media->type === 'image' && $media->image) {
            return asset('public/images/messages/' . $media->image);
        }
        if ($media->type === 'video' && $media->video) {
            return asset('public/videos/messages/' . $media->video);
        }
        if ($media->type === 'audio' && $media->music) {
            return asset('public/music/messages/' . $media->music);
        }
        if ($media->file) {
            return asset('public/files/messages/' . $media->file);
        }
        return null;
    }

    /**
     * Get media thumbnail
     */
    private function getMediaThumbnail($media)
    {
        if ($media->type === 'video' && $media->video) {
            return asset('public/videos/messages/thumbnails/' . pathinfo($media->video, PATHINFO_FILENAME) . '.jpg');
        }
        return null;
    }
}

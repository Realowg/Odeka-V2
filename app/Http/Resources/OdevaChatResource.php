<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OdevaChatResource extends JsonResource
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
            'conversation_id' => $this->conversation_id,
            'message' => $this->message,
            'metadata' => $this->metadata,
        ];
    }
}


<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attachable_type' => $this->attachable_type,
            'attachable_id' => $this->attachable_id,
            'original_name' => $this->original_name,
            'file_url' => $this->file_path ? asset('storage/' . $this->file_path) : null,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'uploader' => new UserResource($this->whenLoaded('uploader')),
            'created_at' => $this->created_at,
        ];
    }
}

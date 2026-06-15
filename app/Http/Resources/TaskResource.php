<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'assigned_to' => $this->assigned_to,
            'creator' => new UserResource($this->whenLoaded('creator')),
            'created_by' => $this->created_by,
            'due_date' => $this->due_date,
            'completed_at' => $this->completed_at,
            'labels' => LabelResource::collection($this->whenLoaded('labels')),
            'subtasks_count' => $this->whenCounted('subtasks'),
            'comments_count' => $this->whenCounted('comments'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

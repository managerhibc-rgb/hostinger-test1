<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ActivityLogController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $logs = ActivityLog::with(['user', 'task'])
            ->when($request->task_id, fn ($q, $id) => $q->where('task_id', $id))
            ->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id))
            ->when($request->action, fn ($q, $action) => $q->where('action', $action))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return ActivityLogResource::collection($logs);
    }
}

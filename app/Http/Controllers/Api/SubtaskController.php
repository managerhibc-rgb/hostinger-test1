<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubtaskRequest;
use App\Http\Requests\Api\UpdateSubtaskRequest;
use App\Http\Resources\SubtaskResource;
use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubtaskController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $subtasks = Subtask::with('task')
            ->when($request->task_id, fn ($q, $id) => $q->where('task_id', $id))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->paginate($request->per_page ?? 15);

        return SubtaskResource::collection($subtasks);
    }

    public function store(StoreSubtaskRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($data['status'] === 'done') {
            $data['completed_at'] = now();
        }

        $subtask = Subtask::create($data);

        return response()->json([
            'message' => 'Subtask created successfully.',
            'data' => new SubtaskResource($subtask->load('task')),
        ], 201);
    }

    public function show(Subtask $subtask): JsonResponse
    {
        return response()->json([
            'data' => new SubtaskResource($subtask->load('task')),
        ]);
    }

    public function update(UpdateSubtaskRequest $request, Subtask $subtask): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['status']) && $data['status'] === 'done' && $subtask->status !== 'done') {
            $data['completed_at'] = now();
        } elseif (isset($data['status']) && $data['status'] !== 'done') {
            $data['completed_at'] = null;
        }

        $subtask->update($data);

        return response()->json([
            'message' => 'Subtask updated successfully.',
            'data' => new SubtaskResource($subtask->load('task')),
        ]);
    }

    public function destroy(Subtask $subtask): JsonResponse
    {
        $subtask->delete();

        return response()->json(['message' => 'Subtask deleted successfully.']);
    }
}

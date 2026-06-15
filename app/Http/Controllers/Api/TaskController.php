<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTaskRequest;
use App\Http\Requests\Api\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $tasks = Task::with(['project', 'assignee', 'creator', 'labels'])
            ->withCount(['subtasks', 'comments'])
            ->when($request->project_id, fn ($q, $id) => $q->where('project_id', $id))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->priority, fn ($q, $priority) => $q->where('priority', $priority))
            ->when($request->assigned_to, fn ($q, $id) => $q->where('assigned_to', $id))
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->paginate($request->per_page ?? 15);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        $labels = $data['labels'] ?? null;
        unset($data['labels']);

        $task = Task::create($data);

        if ($labels) {
            $task->labels()->sync($labels);
        }

        if ($data['status'] === 'done') {
            $task->update(['completed_at' => now()]);
        }

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => new TaskResource($task->load(['project', 'assignee', 'creator', 'labels'])->loadCount(['subtasks', 'comments'])),
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'data' => new TaskResource($task->load([
                'project', 'assignee', 'creator', 'labels', 'subtasks', 'comments' => fn ($q) => $q->with('user'),
            ])->loadCount(['subtasks', 'comments'])),
        ]);
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $data = $request->validated();
        $labels = $data['labels'] ?? null;
        unset($data['labels']);

        if (isset($data['status']) && $data['status'] === 'done' && $task->status !== 'done') {
            $data['completed_at'] = now();
        } elseif (isset($data['status']) && $data['status'] !== 'done') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        if ($labels !== null) {
            $task->labels()->sync($labels);
        }

        return response()->json([
            'message' => 'Task updated successfully.',
            'data' => new TaskResource($task->load(['project', 'assignee', 'creator', 'labels'])->loadCount(['subtasks', 'comments'])),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully.']);
    }
}

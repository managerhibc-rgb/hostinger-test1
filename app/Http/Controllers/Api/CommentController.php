<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $comments = Comment::with(['user', 'task'])
            ->when($request->task_id, fn ($q, $id) => $q->where('task_id', $id))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $comment = Comment::create($data);

        return response()->json([
            'message' => 'Comment created successfully.',
            'data' => new CommentResource($comment->load('user')),
        ], 201);
    }

    public function show(Comment $comment): JsonResponse
    {
        return response()->json([
            'data' => new CommentResource($comment->load('user')),
        ]);
    }

    public function update(Request $request, Comment $comment): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $comment->update($data);

        return response()->json([
            'message' => 'Comment updated successfully.',
            'data' => new CommentResource($comment->load('user')),
        ]);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully.']);
    }
}

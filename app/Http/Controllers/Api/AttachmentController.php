<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttachmentResource;
use App\Models\Attachment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $attachments = Attachment::with('uploader')
            ->when($request->attachable_type, fn ($q, $type) => $q->where('attachable_type', $type))
            ->when($request->attachable_id, fn ($q, $id) => $q->where('attachable_id', $id))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return AttachmentResource::collection($attachments);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'attachable_type' => ['required', 'string', 'in:App\\Models\\Task,App\\Models\\Project,App\\Models\\Comment'],
            'attachable_id' => ['required', 'integer'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $modelClass = $data['attachable_type'];
        $attachable = $modelClass::findOrFail($data['attachable_id']);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = $attachable->attachments()->create([
            'uploaded_by' => $request->user()->id,
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        return response()->json([
            'message' => 'Attachment uploaded successfully.',
            'data' => new AttachmentResource($attachment->load('uploader')),
        ], 201);
    }

    public function show(Attachment $attachment): JsonResponse
    {
        return response()->json([
            'data' => new AttachmentResource($attachment->load('uploader')),
        ]);
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $attachment->delete();

        return response()->json(['message' => 'Attachment deleted successfully.']);
    }
}

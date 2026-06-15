<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LabelResource;
use App\Models\Label;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class LabelController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $labels = Label::withCount('tasks')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->paginate($request->per_page ?? 15);

        return LabelResource::collection($labels);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:labels,name'],
            'color' => ['nullable', 'string', 'max:7'],
            'description' => ['nullable', 'string'],
        ]);

        $label = Label::create($data);

        return response()->json([
            'message' => 'Label created successfully.',
            'data' => new LabelResource($label),
        ], 201);
    }

    public function show(Label $label): JsonResponse
    {
        return response()->json([
            'data' => new LabelResource($label->loadCount('tasks')),
        ]);
    }

    public function update(Request $request, Label $label): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', Rule::unique('labels')->ignore($label->id)],
            'color' => ['nullable', 'string', 'max:7'],
            'description' => ['nullable', 'string'],
        ]);

        $label->update($data);

        return response()->json([
            'message' => 'Label updated successfully.',
            'data' => new LabelResource($label->loadCount('tasks')),
        ]);
    }

    public function destroy(Label $label): JsonResponse
    {
        $label->delete();

        return response()->json(['message' => 'Label deleted successfully.']);
    }
}

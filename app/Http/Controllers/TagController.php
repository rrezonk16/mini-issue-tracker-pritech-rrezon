<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;

class TagController extends Controller
{
    /**
     * List all tags.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tags = Tag::all();

        return response()->json([
            'data' => $tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ];
            }),
        ]);
    }

    /**
     * Create a new tag.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            $tag = Tag::create([
                'name' => $request->name,
                'color' => $request->color,
            ]);

            return response()->json([
                'data' => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'color' => $tag->color,
                ],
                'message' => 'Tag created successfully!',
            ], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create tag. Please try again.'], 500);
        }
    }

    /**
     * Assign tags to an issue.
     *
     * @param Request $request
     * @param Project $project
     * @param Issue $issue
     * @return JsonResponse
     */
    public function assign(Request $request, Project $project, Issue $issue): JsonResponse
    {
        if ($issue->project_id !== $project->id) {
            return response()->json(['error' => 'The issue does not belong to the specified project.'], 404);
        }

        $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:tags,id',
        ]);

        try {
            $issue->tags()->sync($request->tag_ids);

            return response()->json([
                'data' => $issue->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'color' => $tag->color,
                    ];
                }),
                'message' => 'Tags assigned successfully!',
            ]);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to assign tags. Please try again.'], 500);
        }
    }
}
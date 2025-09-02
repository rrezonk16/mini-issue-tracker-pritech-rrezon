<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\QueryException;

class CommentController extends Controller
{
    public function index(Project $project, Issue $issue): JsonResponse
    {
        if ($issue->project_id !== $project->id) {
            return response()->json(['error' => 'The issue does not belong to the specified project.'], 404);
        }

        $comments = $issue->comments()->paginate(5);

        return response()->json([
            'data' => collect($comments->items())->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'author_name' => $comment->author_name,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
                ];
            }),
            'current_page' => $comments->currentPage(),
            'last_page' => $comments->lastPage(),
            'total' => $comments->total(),
            'per_page' => $comments->perPage(),
            'next_page_url' => $comments->nextPageUrl(),
            'prev_page_url' => $comments->previousPageUrl(),
        ]);
    }

    public function store(Request $request, Project $project, Issue $issue): JsonResponse
    {
        if ($issue->project_id !== $project->id) {
            return response()->json(['error' => 'The issue does not belong to the specified project.'], 404);
        }

        $request->validate([
            'author_name' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        try {
            $comment = $issue->comments()->create([
                'author_name' => $request->author_name,
                'body' => $request->body,
                'issue_id' => $issue->id,
            ]);

            return response()->json([
                'data' => [
                    'id' => $comment->id,
                    'author_name' => $comment->author_name,
                    'body' => $comment->body,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
                ],
                'message' => 'Comment added successfully!',
            ], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to add comment. Please try again.'], 500);
        }
    }
}

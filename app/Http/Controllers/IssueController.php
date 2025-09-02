<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class IssueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['apiIndex', 'apiShow']);
    }

    public function index(Project $project)
    {
        $issues = $project->issues()->with(['users'])->get();
        return view('issues.index', compact('project', 'issues'));
    }

    public function create(Project $project)
    {
        $users = User::all();
        return view('issues.create', compact('project', 'users'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:open,in_progress,closed',
            'priority' => 'nullable|string|in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $issue = $project->issues()->create($request->only(['title', 'description', 'status', 'priority', 'due_date']));
            if ($request->has('user_ids')) {
                $issue->users()->sync($request->input('user_ids'));
            } elseif (Auth::check()) {
                $issue->users()->attach(Auth::id(), ['created_at' => now(), 'updated_at' => now()]);
            }
            return redirect()->route('projects.show', $project)->with('success', 'Issue created successfully!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create issue. Please try again.']);
        }
    }

    public function edit(Project $project, Issue $issue)
    {
        if (Auth::id() !== $project->owner_id) {
            return back()->withErrors(['error' => 'Only the project owner can edit issues.']);
        }
        if ($issue->project_id !== $project->id) {
            return back()->withErrors(['error' => 'The issue does not belong to the specified project.']);
        }
        $users = User::all();
        return view('issues.edit', compact('project', 'issue', 'users'));
    }

    public function update(Request $request, Project $project, Issue $issue): RedirectResponse
    {
        if (Auth::id() !== $project->owner_id) {
            return back()->withErrors(['error' => 'Only the project owner can update issues.']);
        }
        if ($issue->project_id !== $project->id) {
            return back()->withErrors(['error' => 'The issue does not belong to the specified project.']);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:open,in_progress,closed',
            'priority' => 'nullable|string|in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $issue->update($request->only(['title', 'description', 'status', 'priority', 'due_date']));
            if ($request->has('user_ids')) {
                $issue->users()->sync($request->input('user_ids'));
            } else {
                $issue->users()->detach();
            }
            return redirect()->route('projects.show', $project)->with('success', 'Issue updated successfully!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to update issue. Please try again.']);
        }
    }

    public function destroy(Project $project, Issue $issue): RedirectResponse
    {
        if (Auth::id() !== $project->owner_id) {
            return back()->withErrors(['error' => 'Only the project owner can delete issues.']);
        }
        if ($issue->project_id !== $project->id) {
            return back()->withErrors(['error' => 'The issue does not belong to the specified project.']);
        }

        try {
            $issue->delete();
            return redirect()->route('projects.show', $project)->with('success', 'Issue deleted successfully!');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Failed to delete issue. Please try again.']);
        }
    }

    public function apiIndex(Project $project): JsonResponse
    {
        $issues = $project->issues()->with(['users'])->get()->map(function ($issue) {
            return [
                'id' => $issue->id,
                'title' => $issue->title,
                'description' => $issue->description,
                'status' => $issue->status ?? 'open',
                'priority' => $issue->priority ?? 'medium',
                'due_date' => $issue->due_date ? $issue->due_date->format('Y-m-d') : null,
                'created_at' => $issue->created_at ? $issue->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $issue->updated_at ? $issue->updated_at->format('Y-m-d H:i:s') : null,
                'users' => $issue->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                }),
            ];
        });

        return response()->json(['data' => $issues]);
    }

    public function apiShow(Project $project, Issue $issue): JsonResponse
    {
        if ($issue->project_id !== $project->id) {
            return response()->json(['error' => 'The issue does not belong to the specified project.'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $issue->id,
                'title' => $issue->title,
                'description' => $issue->description,
                'status' => $issue->status,
                'priority' => $issue->priority,
                'created_at' => $issue->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $issue->updated_at->format('Y-m-d H:i:s'),
                'due_date' => $issue->due_date ? \Carbon\Carbon::parse($issue->due_date)->format('Y-m-d') : null,
                'users' => $issue->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                }),
                'tags' => $issue->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'color' => $tag->color,
                    ];
                })->values(),
            ],
        ]);
    }

    public function apiStore(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:open,in_progress,closed',
            'priority' => 'nullable|string|in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $issue = $project->issues()->create($request->only(['title', 'description', 'status', 'priority', 'due_date']));
            if ($request->has('user_ids')) {
                $issue->users()->sync($request->input('user_ids'));
            } elseif (Auth::check()) {
                $issue->users()->attach(Auth::id(), ['created_at' => now(), 'updated_at' => now()]);
            }
            $issue->load('users');
            return response()->json([
                'data' => [
                    'id' => $issue->id,
                    'title' => $issue->title,
                    'description' => $issue->description,
                    'status' => $issue->status ?? 'open',
                    'priority' => $issue->priority ?? 'medium',
                    'due_date' => $issue->due_date ? $issue->due_date->format('Y-m-d') : null,
                    'created_at' => $issue->created_at ? $issue->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $issue->updated_at ? $issue->updated_at->format('Y-m-d H:i:s') : null,
                    'users' => $issue->users->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                        ];
                    }),
                ]
            ], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create issue. Please try again.'], 500);
        }
    }

    public function apiUpdate(Request $request, Project $project, Issue $issue): JsonResponse
    {
        if (Auth::id() !== $project->owner_id) {
            return response()->json(['error' => 'Only the project owner can update issues.'], 403);
        }
        if ($issue->project_id !== $project->id) {
            return response()->json(['error' => 'The issue does not belong to the specified project.'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:open,in_progress,closed',
            'priority' => 'nullable|string|in:low,medium,high',
            'due_date' => 'nullable|date|after_or_equal:today',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $issue->update($request->only(['title', 'description', 'status', 'priority', 'due_date']));
            if ($request->has('user_ids')) {
                $issue->users()->sync($request->input('user_ids'));
            } else {
                $issue->users()->detach();
            }
            $issue->load('users');
            return response()->json([
                'data' => [
                    'id' => $issue->id,
                    'title' => $issue->title,
                    'description' => $issue->description,
                    'status' => $issue->status ?? 'open',
                    'priority' => $issue->priority ?? 'medium',
                    'due_date' => $issue->due_date ? $issue->due_date->format('Y-m-d') : null,
                    'created_at' => $issue->created_at ? $issue->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $issue->updated_at ? $issue->updated_at->format('Y-m-d H:i:s') : null,
                    'users' => $issue->users->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                        ];
                    }),
                ]
            ]);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update issue. Please try again.'], 500);
        }
    }

    public function apiDestroy(Project $project, Issue $issue): JsonResponse
    {
        if (Auth::id() !== $project->owner_id) {
            return response()->json(['error' => 'Only the project owner can delete issues.'], 403);
        }
        if ($issue->project_id !== $project->id) {
            return response()->json(['error' => 'The issue does not belong to the specified project.'], 404);
        }

        try {
            $issue->delete();
            return response()->json(['message' => 'Issue deleted successfully'], 204);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete issue. Please try again.'], 500);
        }
    }
}

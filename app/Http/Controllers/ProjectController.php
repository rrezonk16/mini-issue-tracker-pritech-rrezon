<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['apiIndex', 'apiShow']);
    }

    public function index()
    {
        $projects = Project::with(['issues', 'users', 'owner'])
            ->where('owner_id', Auth::id())
            ->orWhereHas('users', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->get();
        return view('projects.index', compact('projects'));
    }

    public function owned()
    {
        $projects = Project::with(['issues', 'users', 'owner'])
            ->where('owner_id', Auth::id())
            ->get();
        return view('projects.index', compact('projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $project = Auth::user()->ownedProjects()->create(
                $request->only(['name', 'description', 'start_date', 'deadline'])
            );
            $project->users()->attach(Auth::id(), [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return redirect()->route('projects.show', $project)->with('success', 'Project created successfully!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create project. Please try again.']);
        }
    }

    public function show(Project $project)
    {
        $project->load('issues', 'owner', 'users');
        return view('projects.show', compact('project'));
    }

    public function create()
    {
        $users = User::all();
        return view('projects.create', compact('users'));
    }

    public function edit(Project $project)
    {
        $users = User::all();
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $project->update($request->only(['name', 'description', 'start_date', 'deadline']));
            return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to update project. Please try again.']);
        }
    }

    public function destroy(Project $project): RedirectResponse
    {
        try {
            $project->delete();
            return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Failed to delete project. Please try again.']);
        }
    }

    public function apiIndex(): JsonResponse
    {
        $projects = Project::with(['issues', 'owner', 'users'])->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
                'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                'owner' => $project->owner ? [
                    'id' => $project->owner->id,
                    'name' => $project->owner->name,
                ] : null,
                'issues' => $project->issues->map(function ($issue) {
                    return [
                        'id' => $issue->id,
                        'title' => $issue->title,
                        'description' => $issue->description,
                        'status' => $issue->status ?? 'open',
                        'priority' => $issue->priority ?? 'low',
                        'users' => $issue->users->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json(['data' => $projects]);
    }

    public function apiShow(Project $project): JsonResponse
    {
        $project->load(['issues', 'owner', 'users']);
        return response()->json([
            'data' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
                'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                'owner' => $project->owner ? [
                    'id' => $project->owner->id,
                    'name' => $project->owner->name,
                ] : null,
                'issues' => $project->issues->map(function ($issue) {
                    return [
                        'id' => $issue->id,
                        'title' => $issue->title,
                        'description' => $issue->description,
                        'status' => $issue->status ?? 'open',
                        'priority' => $issue->priority ?? 'low',
                        'users' => $issue->users->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->name,
                            ];
                        }),
                    ];
                }),
            ]
        ]);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $project = Auth::check()
                ? Auth::user()->ownedProjects()->create($request->only(['name', 'description', 'start_date', 'deadline']))
                : Project::create($request->only(['name', 'description', 'start_date', 'deadline']));
            if (Auth::check()) {
                $project->users()->attach(Auth::id(), [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $project->load('issues', 'owner', 'users');
            return response()->json([
                'data' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
                    'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                    'owner' => $project->owner ? [
                        'id' => $project->owner->id,
                        'name' => $project->owner->name,
                    ] : null,
                    'issues' => [],
                ]
            ], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create project. Please try again.'], 500);
        }
    }

    public function apiUpdate(Request $request, Project $project): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date|after_or_equal:start_date',
        ]);

        try {
            $project->update($request->only(['name', 'description', 'start_date', 'deadline']));
            $project->load('issues', 'owner', 'users');
            return response()->json([
                'data' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
                    'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                    'owner' => $project->owner ? [
                        'id' => $project->owner->id,
                        'name' => $project->owner->name,
                    ] : null,
                    'issues' => $project->issues->map(function ($issue) {
                        return [
                            'id' => $issue->id,
                            'title' => $issue->title,
                            'description' => $issue->description,
                            'status' => $issue->status ?? 'open',
                            'priority' => $issue->priority ?? 'low',
                            'users' => $issue->users->map(function ($user) {
                                return [
                                    'id' => $user->id,
                                    'name' => $user->name,
                                ];
                            }),
                        ];
                    }),
                ]
            ]);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update project. Please try again.'], 500);
        }
    }

    public function apiDestroy(Project $project): JsonResponse
    {
        try {
            $project->delete();
            return response()->json(['message' => 'Project deleted successfully'], 204);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete project. Please try again.'], 500);
        }
    }
}
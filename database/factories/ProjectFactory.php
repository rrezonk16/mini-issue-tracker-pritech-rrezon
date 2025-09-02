<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['apiIndex', 'apiShow']);
    }

    public function index()
    {
        $projects = Auth::user()->ownedProjects()->with(['owner', 'users'])->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $users = User::all();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|before_or_equal:today',
            'deadline' => 'nullable|date|after_or_equal:today',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'deadline' => $request->deadline,
                'owner_id' => Auth::id(),
            ]);
            if ($request->has('user_ids')) {
                $project->users()->sync($request->input('user_ids'));
            }
            return redirect()->route('projects.show', $project)->with('success', 'Project created successfully!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to create project. Please try again.']);
        }
    }

    public function show(Project $project)
    {
        $project->load(['issues.users', 'owner', 'users']);
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        if (Auth::id() !== $project->owner_id) {
            return back()->withErrors(['error' => 'Only the project owner can edit this project.']);
        }
        $users = User::all();
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        if (Auth::id() !== $project->owner_id) {
            return back()->withErrors(['error' => 'Only the project owner can update this project.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|before_or_equal:today',
            'deadline' => 'nullable|date|after_or_equal:today',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $project->update($request->only(['name', 'description', 'start_date', 'deadline']));
            if ($request->has('user_ids')) {
                $project->users()->sync($request->input('user_ids'));
            } else {
                $project->users()->detach();
            }
            return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully!');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to update project. Please try again.']);
        }
    }

    public function destroy(Project $project): RedirectResponse
    {
        if (Auth::id() !== $project->owner_id) {
            return back()->withErrors(['error' => 'Only the project owner can delete this project.']);
        }

        try {
            $project->delete();
            return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Failed to delete project. Please try again.']);
        }
    }

    public function apiIndex(): JsonResponse
    {
        $projects = Project::with(['owner', 'users'])->get()->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
                'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                'created_at' => $project->created_at ? $project->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $project->updated_at ? $project->updated_at->format('Y-m-d H:i:s') : null,
                'owner' => $project->owner ? ['id' => $project->owner->id, 'name' => $project->owner->name] : null,
                'users' => $project->users->map(function ($user) {
                    return ['id' => $user->id, 'name' => $user->name];
                }),
            ];
        });

        return response()->json(['data' => $projects]);
    }

    public function apiShow(Project $project): JsonResponse
    {
        $project->load(['owner', 'users']);
        return response()->json([
            'data' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
                'created_at' => $project->created_at ? $project->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $project->updated_at ? $project->updated_at->format('Y-m-d H:i:s') : null,
                'owner' => $project->owner ? ['id' => $project->owner->id, 'name' => $project->owner->name] : null,
                'users' => $project->users->map(function ($user) {
                    return ['id' => $user->id, 'name' => $user->name];
                }),
            ]
        ]);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date|before_or_equal:today',
            'deadline' => 'nullable|date|after_or_equal:today',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        try {
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'deadline' => $request->deadline,
                'owner_id' => Auth::id(),
            ]);
            if ($request->has('user_ids')) {
                $project->users()->sync($request->input('user_ids'));
            }
            $project->load(['owner', 'users']);
            return response()->json([
                'data' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'start_date' => $project->start_date ? $project->start_date->format('Y-m-d') : null,
                    'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                    'created_at' => $project->created_at ? $project->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $project->updated_at ? $project->updated_at->format('Y-m-d H:i:s') : null,
                    'owner' => $project->owner ? ['id' => $project->owner->id, 'name' => $project->owner->name] : null,
                    'users' => $project->users->map(function ($user) {
                        return ['id' => $user->id, 'name' => $user->name];
                    }),
                ]
            ], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create project. Please try again.'], 500);
        }
    }
}
@props(['project'])

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $project->name }}</h1>
        <p class="text-gray-600 mt-2 max-w-2xl">{{ $project->description }}</p>
        <p class="text-gray-500 text-sm mt-1">Owner: {{ $project->owner ? $project->owner->name : 'Not assigned' }}</p>
    </div>
    @auth
        <div class="flex items-center space-x-4">
            @if (Auth::id() === $project->owner_id)
                <a href="{{ route('projects.edit', $project) }}"
                   class="bg-green-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-green-700 transition duration-300 shadow-md hover:shadow-lg">
                    Edit Project
                </a>
                <form action="{{ route('projects.destroy', $project) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to delete this project?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-red-700 transition duration-300 shadow-md hover:shadow-lg">
                        Delete Project
                    </button>
                </form>
            @endif
            <a href="{{ route('issues.create', $project) }}"
               class="bg-blue-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-blue-700 transition duration-300 shadow-md hover:shadow-lg">
                + Add Issue
            </a>
        </div>
    @endauth
</div>
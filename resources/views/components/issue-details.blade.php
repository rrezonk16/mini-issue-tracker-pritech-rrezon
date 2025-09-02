@props(['project'])

<div class=" bg-white rounded-2xl shadow-md p-6 issue-details">
    @if($project->issues->count())
        @php
            $selectedIssue = $project->issues->first();
        @endphp
        <h2 class="text-xl font-semibold text-gray-800 mb-4 issue-title">{{ $selectedIssue->title }}</h2>
        <p class="text-gray-600 mb-4 issue-description">{{ $selectedIssue->description }}</p>
        <p class="text-gray-500 text-sm mb-4 issue-users">Assigned: {{ $selectedIssue->users->pluck('name')->join(', ') ?: 'None' }}</p>
        <div class="flex flex-wrap gap-2 mb-4 issue-tags">
            @foreach($selectedIssue->tags as $tag)
                <span class="px-2 py-1 text-xs font-medium rounded-full"
                      style="background-color: {{ $tag->color ?? '#e5e7eb' }}; color: {{ $tag->color && preg_match('/^#[0-5][0-9a-fA-F]{0,5}$/', $tag->color) ? '#fff' : '#000' }}">
                    {{ $tag->name }}
                </span>
            @endforeach
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 issue-meta">
            <div>
                <span class="text-sm font-medium text-gray-700">Status:</span>
                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full {{ $selectedIssue->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} issue-status">
                    {{ ucfirst($selectedIssue->status ?? 'open') }}
                </span>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-700">Priority:</span>
                <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full {{ $selectedIssue->priority === 'high' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }} issue-priority">
                    {{ ucfirst($selectedIssue->priority ?? 'low') }}
                </span>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-700">Created:</span>
                <span class="ml-2 text-sm text-gray-600 issue-created">{{ \Carbon\Carbon::parse($selectedIssue->created_at)->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-700">Updated:</span>
                <span class="ml-2 text-sm text-gray-600 issue-updated">{{ \Carbon\Carbon::parse($selectedIssue->updated_at)->format('M d, Y') }}</span>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-700">Due Date:</span>
                <span class="ml-2 text-sm text-gray-600 issue-due_date">{{ $selectedIssue->due_date ? \Carbon\Carbon::parse($selectedIssue->due_date)->format('M d, Y') : 'Not set' }}</span>
            </div>
        </div>
        @auth
            @if (Auth::id() === $project->owner_id)
                <div class="mt-4 flex space-x-4">
                    <a href="{{ route('issues.edit', [$project, $selectedIssue]) }}"
                       class="text-blue-600 hover:underline">Edit Issue</a>
                    <form action="{{ route('issues.destroy', [$project, $selectedIssue]) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this issue?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete Issue</button>
                    </form>
                </div>
            @endif
        @endauth
    @else
        <p class="text-gray-500">Select an issue from the list to view details.</p>
    @endif
</div>
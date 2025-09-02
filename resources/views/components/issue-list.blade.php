@props(['project'])

<div class="lg:w-1/3 bg-white rounded-2xl shadow-md p-4 max-h-[calc(100vh-250px)] overflow-y-auto issue-list">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Issues</h2>
    @if($project->issues->count())
        @foreach($project->issues as $index => $issue)
            <div class="issue-card block p-4 border-b border-gray-200 hover:bg-gray-50 {{ $index === 0 ? 'selected-issue' : '' }}"
                 data-issue-id="{{ $issue->id }}"
                 onclick="loadIssueDetails({{ $issue->id }})">
                <h3 class="text-sm font-medium text-gray-800 line-clamp-1">{{ $issue->title }}</h3>
                <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ Str::limit($issue->description, 50) }}</p>
                <p class="text-xs text-gray-500 mt-1">Assigned: {{ $issue->users->pluck('name')->join(', ') ?: 'None' }}</p>
                <div class="flex justify-between text-xs text-gray-500 mt-2">
                    <span class="px-2 py-1 rounded-full {{ $issue->status === 'open' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($issue->status ?? 'open') }}
                    </span>
                    <span class="px-2 py-1 rounded-full {{ $issue->priority === 'high' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($issue->priority ?? 'low') }}
                    </span>
                </div>
                <div class="flex flex-wrap gap-1 mt-2 issue-tags-list">
                    @foreach($issue->tags as $tag)
                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                              style="background-color: {{ $tag->color ?? '#e5e7eb' }}; color: {{ $tag->color && preg_match('/^#[0-5][0-9a-fA-F]{0,5}$/', $tag->color) ? '#fff' : '#000' }}">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>
                @auth
                    @if (Auth::id() === $project->owner_id)
                        <div class="mt-2 flex justify-end space-x-2">
                            <a href="{{ route('issues.edit', [$project, $issue]) }}"
                               class="text-blue-600 text-xs hover:underline font-medium">Edit</a>
                            <form action="{{ route('issues.destroy', [$project, $issue]) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this issue?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 text-xs hover:underline font-medium">Delete</button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>
        @endforeach
    @else
        <p class="text-gray-500 text-sm">No issues yet. Add one above.</p>
    @endif
</div>
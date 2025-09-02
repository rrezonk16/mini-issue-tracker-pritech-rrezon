<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Issue - PRITECH Issue Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        input:focus, select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        button {
            transition: all 0.2s ease;
        }
        button:hover {
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">
    <div class="container mx-auto px-6 py-12 flex-grow">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg text-sm">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h1 class="text-2xl font-bold text-gray-800 mb-8">Edit Issue for {{ $project->name }}</h1>
        <form action="{{ route('issues.update', [$project, $issue]) }}" method="POST" class="space-y-5 max-w-md">
            @csrf
            @method('PUT')
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $issue->title) }}"
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description"
                          class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $issue->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="open" {{ old('status', $issue->status) == 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ old('status', $issue->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="closed" {{ old('status', $issue->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                <select name="priority" id="priority"
                        class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="low" {{ old('priority', $issue->priority) == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority', $issue->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority', $issue->priority) == 'high' ? 'selected' : '' }}>High</option>
                </select>
                @error('priority')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $issue->due_date ? $issue->due_date->format('Y-m-d') : '') }}"
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                @error('due_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Assigned Users</label>
                <div class="mt-1 max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-2">
                    @foreach ($users as $user)
                        <div class="flex items-center mb-2">
                            <input type="checkbox" name="user_ids[]" id="user_{{ $user->id }}"
                                   value="{{ $user->id }}"
                                   {{ in_array($user->id, old('user_ids', $issue->users->pluck('id')->toArray())) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="user_{{ $user->id }}" class="ml-2 text-sm text-gray-700">{{ $user->name }}</label>
                        </div>
                    @endforeach
                </div>
                @error('user_ids')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('projects.show', $project) }}"
                   class="px-4 py-2 text-gray-600 font-semibold rounded-lg hover:bg-gray-100">Cancel</a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">Update Issue</button>
            </div>
        </form>
    </div>
</body>
</html>
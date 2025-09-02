<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - PRITECH Issue Tracker</title>
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
        <h1 class="text-2xl font-bold text-gray-800 mb-8">Edit Project</h1>
        <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-5 max-w-md">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Project Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}"
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description"
                          class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date ? $project->start_date   : '') }}"
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                @error('start_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $project->deadline ? $project->deadline   : '') }}"
                       class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                @error('deadline')
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
                                   {{ in_array($user->id, old('user_ids', $project->users->pluck('id')->toArray())) ? 'checked' : '' }}
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
                        class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">Update Project</button>
            </div>
        </form>
    </div>
</body>
</html>
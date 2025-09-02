@props(['project'])

@auth
    @if (Auth::id() === $project->owner_id)
        <div class="mt-4 bg-white rounded-2xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Create New Tag</h3>
            <form id="create-tag-form" class="space-y-4">
                @csrf
                <div>
                    <label for="tag_name" class="block text-sm font-medium text-gray-700">Tag Name</label>
                    <input type="text" name="tag_name" id="tag_name"
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           required>
                    <p id="tag_name-error" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>
                <div>
                    <label for="tag_color" class="block text-sm font-medium text-gray-700">Tag Color (Hex)</label>
                    <input type="text" name="tag_color" id="tag_color"
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="#FF0000">
                    <p id="tag_color-error" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-300">
                        Create Tag
                    </button>
                </div>
            </form>
        </div>
    @endif
@endauth
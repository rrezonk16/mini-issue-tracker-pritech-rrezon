@props(['project'])

<div class="mt-8 bg-white rounded-2xl shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Comments</h3>
    <div id="comments-list" class="space-y-4">
        <!-- Comments will be loaded here via AJAX -->
    </div>
    <!-- Pagination Controls -->
    <div id="comments-pagination" class="mt-4 flex justify-between items-center">
        <button id="prev-page" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 disabled:opacity-50" disabled>Previous</button>
        <span id="page-info" class="text-sm text-gray-600"></span>
        <button id="next-page" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 disabled:opacity-50" disabled>Next</button>
    </div>
    <!-- Comment Form -->
    <form id="comment-form" class="mt-6 space-y-4">
        @csrf
        <div>
            <label for="author_name" class="block text-sm font-medium text-gray-700">Your Name</label>
            <input type="text" name="author_name" id="author_name" value="{{ Auth::check() ? Auth::user()->name : '' }}"
                   class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                   required>
            <p id="author_name-error" class="text-red-500 text-sm mt-1 hidden"></p>
        </div>
        <div>
            <label for="body" class="block text-sm font-medium text-gray-700">Comment</label>
            <textarea name="body" id="body" rows="4"
                      class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                      required></textarea>
            <p id="body-error" class="text-red-500 text-sm mt-1 hidden"></p>
        </div>
        <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300">
                Add Comment
            </button>
        </div>
    </form>
</div>
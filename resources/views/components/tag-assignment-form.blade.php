@props(['project'])

@auth
    @if (Auth::id() === $project->owner_id)
        <div class="mt-6 bg-white rounded-2xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Assign Tags</h3>
            <form id="tag-form" class="space-y-4">
                @csrf
                <div>
                    <label for="tag_ids" class="block text-sm font-medium text-gray-700">Select Tags</label>
                    <select id="tag_ids" name="tag_ids[]" multiple
                            class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 select2-tags">
                    </select>
                    <p id="tag_ids-error" class="text-red-500 text-sm mt-1 hidden"></p>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300">
                        Assign Tags
                    </button>
                </div>
            </form>
        </div>
    @endif
@endauth

@push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (document.querySelector('.select2-tags')) {
                $('.select2-tags').select2({
                    placeholder: 'Select tags',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    </script>
@endpush
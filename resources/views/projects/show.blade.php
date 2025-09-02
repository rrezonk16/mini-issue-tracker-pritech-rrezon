<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $project->name }} - PRITECH Issue Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .issue-list::-webkit-scrollbar {
            width: 6px;
        }
        .issue-list::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .issue-list::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 3px;
        }
        .issue-list::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }
        .issue-card {
            transition: background-color 0.2s ease, transform 0.2s ease;
        }
        .issue-card:hover {
            background-color: #f8fafc;
        }
        .selected-issue {
            background-color: #e0f2fe;
            border-left: 3px solid #3b82f6;
        }
        .logo-text {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .details-loading {
            opacity: 0.5;
            pointer-events: none;
        }
        .select2-container .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 0.25rem;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">
    <x-navbar :project="$project" />

    <div class="container mx-auto px-6 py-8 flex-grow">
        <div class="mb-6">
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline text-sm font-medium">
                &larr; Back to Projects
            </a>
        </div>

        <x-project-header :project="$project" />

        <div class="flex flex-col lg:flex-row gap-6">
            <x-issue-list :project="$project" />
            <div class="lg:w-2/3">
                <x-issue-details :project="$project" />
                @auth
                    @if (Auth::id() === $project->owner_id)
                        <x-tag-assignment-form :project="$project" />
                        <x-create-tag-form :project="$project" />
                    @endif
                @endauth
                <x-comments-section :project="$project" />
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-white py-6 mt-auto">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} PRITECH Issue Tracker. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const issueList = document.querySelector('.issue-list');
            if (issueList) {
                issueList.classList.add('opacity-0');
                setTimeout(() => {
                    issueList.classList.remove('opacity-0');
                    issueList.classList.add('opacity-100', 'transition', 'duration-500');
                }, 100);
            }

            @if($project->issues->count())
                loadIssueDetails({{ $project->issues->first()->id }});
            @endif
        });

        async function loadIssueDetails(issueId) {
            const detailsPanel = document.querySelector('.issue-details');
            const issueCards = document.querySelectorAll('.issue-card');
            const projectId = {{ $project->id }};

            if (!detailsPanel) {
                console.error('Details panel not found');
                return;
            }

            detailsPanel.classList.add('details-loading');

            issueCards.forEach(card => card.classList.remove('selected-issue'));
            const selectedCard = document.querySelector(`[data-issue-id="${issueId}"]`);
            let tagsList = null;
            if (selectedCard) {
                selectedCard.classList.add('selected-issue');
                tagsList = selectedCard.querySelector('.issue-tags-list');
                if (tagsList) {
                    tagsList.innerHTML = '<span class="text-gray-500 text-xs">Loading tags...</span>';
                } else {
                    console.warn(`No .issue-tags-list found for issue ${issueId}`);
                }
            } else {
                console.warn(`No issue card found for issue ${issueId}`);
            }

            try {
                const response = await fetch(`/api/projects/${projectId}/issues/${issueId}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const issue = await response.json();

                console.log('Issue API Response:', issue);

                const issueData = issue.data || issue;

                const title = document.querySelector('.issue-title');
                const description = document.querySelector('.issue-description');
                const status = document.querySelector('.issue-status');
                const priority = document.querySelector('.issue-priority');
                const created = document.querySelector('.issue-created');
                const updated = document.querySelector('.issue-updated');
                const due_date = document.querySelector('.issue-due_date');
                const users = document.querySelector('.issue-users');
                const tags = document.querySelector('.issue-tags');

                if (title) title.textContent = issueData.title || 'No title';
                if (description) description.textContent = issueData.description || 'No description';
                if (status) {
                    const statusText = issueData.status ? issueData.status.charAt(0).toUpperCase() + issueData.status.slice(1) : 'Open';
                    status.textContent = statusText;
                    status.className = `ml-2 px-2 py-1 text-xs font-medium rounded-full ${issueData.status === 'open' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`;
                }
                if (priority) {
                    const priorityText = issueData.priority ? issueData.priority.charAt(0).toUpperCase() + issueData.priority.slice(1) : 'Low';
                    priority.textContent = priorityText;
                    priority.className = `ml-2 px-2 py-1 text-xs font-medium rounded-full ${issueData.priority === 'high' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'}`;
                }
                if (created) created.textContent = issueData.created_at ? new Date(issueData.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Not set';
                if (updated) updated.textContent = issueData.updated_at ? new Date(issueData.updated_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Not set';
                if (due_date) due_date.textContent = issueData.due_date ? new Date(issueData.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'Not set';
                if (users) users.textContent = 'Assigned: ' + (issueData.users && issueData.users.length ? issueData.users.map(user => user.name).join(', ') : 'None');
                if (tags) {
                    tags.innerHTML = issueData.tags && issueData.tags.length ? issueData.tags.map(tag => `
                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                              style="background-color: ${tag.color || '#e5e7eb'}; color: ${tag.color && /#[0-5][0-9a-fA-F]{0,5}/.test(tag.color) ? '#fff' : '#000'}">
                            ${tag.name}
                        </span>
                    `).join(' ') : '<span class="text-gray-500 text-sm">No tags assigned</span>';
                } else {
                    console.warn('No .issue-tags element found in issue-details');
                }

                if (selectedCard && tagsList) {
                    tagsList.innerHTML = issueData.tags && issueData.tags.length ? issueData.tags.map(tag => `
                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                              style="background-color: ${tag.color || '#e5e7eb'}; color: ${tag.color && /#[0-5][0-9a-fA-F]{0,5}/.test(tag.color) ? '#fff' : '#000'}">
                            ${tag.name}
                        </span>
                    `).join(' ') : '<span class="text-gray-500 text-xs">No tags assigned</span>';
                }

                loadComments(issueId, 1);
                @auth
                    @if (Auth::id() === $project->owner_id)
                        loadTags(issueId);
                    @endif
                @endauth
            } catch (error) {
                console.error('Error loading issue:', error);
                detailsPanel.innerHTML = '<p class="text-red-500">Error loading issue details. Please try again.</p>';
            } finally {
                detailsPanel.classList.remove('details-loading');
            }
        }

        async function loadComments(issueId, page = 1) {
            const commentsList = document.querySelector('#comments-list');
            const prevPageButton = document.querySelector('#prev-page');
            const nextPageButton = document.querySelector('#next-page');
            const pageInfo = document.querySelector('#page-info');
            const projectId = {{ $project->id }};

            if (!commentsList) {
                console.error('Comments list element not found');
                return;
            }

            commentsList.innerHTML = '<p class="text-gray-500">Loading comments...</p>';

            try {
                const response = await fetch(`/api/projects/${projectId}/issues/${issueId}/comments?page=${page}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    console.error('Error loading comments:', {
                        status: response.status,
                        statusText: response.statusText,
                        error: errorData.error || 'No error message provided',
                    });
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const data = await response.json();

                console.log('Comments API Response:', data);

                commentsList.innerHTML = '';
                if (data.data.length === 0) {
                    commentsList.innerHTML = '<p class="text-gray-500">No comments yet.</p>';
                } else {
                    data.data.forEach(comment => {
                        const commentElement = document.createElement('div');
                        commentElement.className = 'border-b border-gray-200 pb-4';
                        commentElement.innerHTML = `
                            <p class="text-sm font-medium text-gray-700">${comment.author_name}</p>
                            <p class="text-sm text-gray-600 mt-1">${comment.body}</p>
                            <p class="text-xs text-gray-500 mt-1">${new Date(comment.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: 'numeric' })}</p>
                        `;
                        commentsList.appendChild(commentElement);
                    });
                }

                if (pageInfo) pageInfo.textContent = `Page ${data.current_page} of ${data.last_page}`;
                if (prevPageButton) prevPageButton.disabled = !data.prev_page_url;
                if (nextPageButton) nextPageButton.disabled = !data.next_page_url;
                if (prevPageButton) prevPageButton.onclick = () => loadComments(issueId, data.current_page - 1);
                if (nextPageButton) nextPageButton.onclick = () => loadComments(issueId, data.current_page + 1);
            } catch (error) {
                console.error('Error loading comments:', error);
                commentsList.innerHTML = '<p class="text-red-500">Error loading comments. Please try again.</p>';
            }
        }

        async function loadTags(issueId) {
            const tagSelect = document.querySelector('#tag_ids');
            if (!tagSelect) {
                console.error('Tag select element not found');
                return;
            }

            try {
                const response = await fetch('/api/tags', {
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const tags = await response.json();

                console.log('Tags API Response:', tags);

                tagSelect.innerHTML = '';
                tags.data.forEach(tag => {
                    const option = document.createElement('option');
                    option.value = tag.id;
                    option.textContent = tag.name;
                    tagSelect.appendChild(option);
                });

                const issueResponse = await fetch(`/api/projects/{{ $project->id }}/issues/${issueId}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });
                if (!issueResponse.ok) {
                    throw new Error(`HTTP error! Status: ${issueResponse.status}`);
                }
                const issue = await issueResponse.json();
                const issueTags = issue.data?.tags || issue.tags || [];
                tagSelect.querySelectorAll('option').forEach(option => {
                    option.selected = issueTags.some(tag => tag.id === parseInt(option.value));
                });

                $(tagSelect).select2({
                    placeholder: 'Select tags',
                    allowClear: true,
                    width: '100%'
                });
            } catch (error) {
                console.error('Error loading tags:', error);
                tagSelect.innerHTML = '<option>Error loading tags</option>';
            }
        }

        const tagForm = document.querySelector('#tag-form');
        if (tagForm) {
            tagForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(tagForm);
                const issueId = document.querySelector('.selected-issue')?.dataset.issueId;
                const projectId = {{ $project->id }};
                const tagIdsError = document.querySelector('#tag_ids-error');

                if (!issueId) {
                    console.error('No selected issue found');
                    if (tagIdsError) {
                        tagIdsError.textContent = 'No issue selected';
                        tagIdsError.classList.remove('hidden');
                    }
                    return;
                }

                if (tagIdsError) tagIdsError.classList.add('hidden');

                try {
                    const selectedTagIds = Array.from(formData.getAll('tag_ids[]')).map(id => parseInt(id));
                    console.log('Selected Tag IDs:', selectedTagIds);
                    const response = await fetch(`/api/projects/${projectId}/issues/${issueId}/tags`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            tag_ids: selectedTagIds,
                        }),
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422 && tagIdsError && result.errors?.tag_ids) {
                            tagIdsError.textContent = result.errors.tag_ids[0];
                            tagIdsError.classList.remove('hidden');
                        } else {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return;
                    }

                    console.log('Tag Assignment Response:', result);
                    loadIssueDetails(issueId);
                } catch (error) {
                    console.error('Error assigning tags:', error);
                    if (tagIdsError) {
                        tagIdsError.textContent = 'Error assigning tags. Please try again.';
                        tagIdsError.classList.remove('hidden');
                    }
                }
            });
        }

        const createTagForm = document.querySelector('#create-tag-form');
        if (createTagForm) {
            createTagForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(createTagForm);
                const tagNameError = document.querySelector('#tag_name-error');
                const tagColorError = document.querySelector('#tag_color-error');

                if (tagNameError) tagNameError.classList.add('hidden');
                if (tagColorError) tagColorError.classList.add('hidden');

                try {
                    const response = await fetch('/api/tags', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            name: formData.get('tag_name'),
                            color: formData.get('tag_color'),
                        }),
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422) {
                            if (result.errors?.name && tagNameError) {
                                tagNameError.textContent = result.errors.name[0];
                                tagNameError.classList.remove('hidden');
                            }
                            if (result.errors?.color && tagColorError) {
                                tagColorError.textContent = result.errors.color[0];
                                tagColorError.classList.remove('hidden');
                            }
                        } else {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return;
                    }

                    createTagForm.reset();
                    const issueId = document.querySelector('.selected-issue')?.dataset.issueId;
                    if (issueId) {
                        loadTags(issueId);
                    } else {
                        console.warn('No selected issue to reload tags');
                    }
                } catch (error) {
                    console.error('Error creating tag:', error);
                    if (tagNameError) {
                        tagNameError.textContent = 'Error creating tag. Please try again.';
                        tagNameError.classList.remove('hidden');
                    }
                }
            });
        }

        const commentForm = document.querySelector('#comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(commentForm);
                const issueId = document.querySelector('.selected-issue')?.dataset.issueId;
                const projectId = {{ $project->id }};
                const authorName = document.querySelector('#author_name');
                const body = document.querySelector('#body');
                const authorNameError = document.querySelector('#author_name-error');
                const bodyError = document.querySelector('#body-error');

                if (!issueId) {
                    console.error('No selected issue found');
                    if (bodyError) {
                        bodyError.textContent = 'No issue selected';
                        bodyError.classList.remove('hidden');
                    }
                    return;
                }

                if (authorNameError) authorNameError.classList.add('hidden');
                if (bodyError) bodyError.classList.add('hidden');

                try {
                    const response = await fetch(`/api/projects/${projectId}/issues/${issueId}/comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            author_name: formData.get('author_name'),
                            body: formData.get('body'),
                        }),
                    });

                    const result = await response.json();

                    if (!response.ok) {
                        if (response.status === 422) {
                            if (result.errors?.author_name && authorNameError) {
                                authorNameError.textContent = result.errors.author_name[0];
                                authorNameError.classList.remove('hidden');
                            }
                            if (result.errors?.body && bodyError) {
                                bodyError.textContent = result.errors.body[0];
                                bodyError.classList.remove('hidden');
                            }
                        } else {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return;
                    }

                    commentForm.reset();
                    loadComments(issueId, 1);
                } catch (error) {
                    console.error('Error adding comment:', error);
                    const commentsList = document.querySelector('#comments-list');
                    if (commentsList) {
                        commentsList.innerHTML = '<p class="text-red-500">Error adding comment. Please try again.</p>';
                    }
                }
            });
        }
    </script>
</body>
</html>
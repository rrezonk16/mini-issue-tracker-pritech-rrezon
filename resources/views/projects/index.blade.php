<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRITECH Issue Tracker - Projects</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .logo-text {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .modal {
            transition: all 0.3s ease-in-out;
        }
        .modal-open {
            overflow: hidden;
        }
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
    <!-- Navbar -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-3xl font-extrabold logo-text tracking-tight">
                PRITECH Issue Tracker
            </a>
            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-blue-700 transition duration-300">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="bg-blue-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-blue-700 transition duration-300">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="text-blue-600 px-5 py-2 rounded-full font-semibold hover:bg-gray-100 transition duration-300">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12 flex-grow">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">{{ request()->routeIs('projects.owned') ? 'Your Owned Projects' : 'Projects' }}</h1>
            @auth
                <div class="flex space-x-4">
                    <button type="button" onclick="openModal('createProjectModal')"
                            class="bg-blue-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-blue-700 transition duration-300">
                        New Project
                    </button>
                    <a href="{{ route('projects.owned') }}"
                       class="bg-blue-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-blue-700 transition duration-300">
                        Show Your Projects
                    </a>
                </div>
            @endauth
        </div>

        <!-- Project List -->
        @if ($projects->isEmpty())
            <p class="text-gray-600">No projects found. @auth Create one to get started! @endauth</p>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300">
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $project->name }}</h2>
                        <p class="text-gray-600 mb-4">{{ $project->description ?? 'No description' }}</p>
                        <p class="text-sm text-gray-500">Issues: {{ $project->issues->count() }}</p>
                        <a href="{{ route('projects.show', $project) }}"
                           class="mt-4 inline-block text-blue-600 hover:underline font-medium">
                            View Details
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        @auth
            <div id="createProjectModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden modal">
                <div class="bg-white rounded-2xl p-8 w-full max-w-md">
                    <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Create New Project</h2>
                    <form action="{{ route('projects.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Project Name</label>
                            <input type="text" name="name" id="name" placeholder="Enter project name" value="{{ old('name') }}"
                                   class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-200"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" placeholder="Enter project description"
                                      class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-200">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                   class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-200">
                            @error('start_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="deadline" class="block text-sm font-medium text-gray-700">Deadline</label>
                            <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}"
                                   class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-200">
                            @error('deadline')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeModal('createProjectModal')"
                                    class="px-4 py-2 text-gray-600 font-semibold rounded-lg hover:bg-gray-100 transition duration-200">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300 shadow-md hover:shadow-lg">
                                Create Project
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endauth
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-auto">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} PRITECH Issue Tracker. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript for Modal -->
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.classList.add('modal-open');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.classList.remove('modal-open');
        }

        document.getElementById('createProjectModal').addEventListener('click', function (event) {
            if (event.target === this) {
                closeModal('createProjectModal');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeModal('createProjectModal');
            }
        });
    </script>
</body>
</html>
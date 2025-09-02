<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PRITECH Issue Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .logo-text {
            background: linear-gradient(90deg, #3b82f6, #60a5fa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form-container {
            transition: all 0.3s ease-in-out;
            transform: scale(1);
        }
        .form-container:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        input:focus {
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
                <a href="{{ route('login') }}" class="bg-blue-600 text-white px-5 py-2 rounded-full font-semibold hover:bg-blue-700 transition duration-300">
                    Login
                </a>
                <a href="{{ route('register') }}" class="text-blue-600 px-5 py-2 rounded-full font-semibold hover:bg-gray-100 transition duration-300">
                    Register
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12 flex-grow flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-lg p-10 w-full max-w-md form-container">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Create Your Account</h1>
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" placeholder="Enter your full name" value="{{ old('name') }}"
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-200"
                           required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" value="{{ old('email') }}"
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-200"
                           required>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password"
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-200"
                           required>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm your password"
                           class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none transition duration-200"
                           required>
                    @error('password_confirmation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition duration-300 shadow-md hover:shadow-lg">
                    Register
                </button>
                <p class="text-center text-sm text-gray-600 mt-4">
                    Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Login Here</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-auto">
        <div class="container mx-auto px-6 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} PRITECH Issue Tracker. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript for Animation -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const formContainer = document.querySelector('.form-container');
            formContainer.classList.add('opacity-0', 'translate-y-4');
            setTimeout(() => {
                formContainer.classList.remove('opacity-0', 'translate-y-4');
                formContainer.classList.add('opacity-100', 'translate-y-0', 'transition', 'duration-500', 'ease-out');
            }, 100);
        });
    </script>
</body>
</html>
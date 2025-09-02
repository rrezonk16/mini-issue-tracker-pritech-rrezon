@props(['project'])

<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <a href="{{ route('home') }}" class="text-2xl font-extrabold logo-text tracking-tight">
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
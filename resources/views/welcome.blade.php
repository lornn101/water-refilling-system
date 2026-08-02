<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Poblacion Water Refilling Station</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased">
    <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 text-center">
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-10">
                <h1 class="text-4xl font-bold text-blue-600 mb-4">💧 Water Refilling Station</h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-2">Poblacion, Bilar, Bohol</p>
                <p class="text-lg text-gray-500 dark:text-gray-400 mb-8">Pure, Clean, and Delivered to Your Doorstep</p>
                
                <div class="flex justify-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Register Now
                            </a>
                        @endauth
                    @endif
                </div>

                <div class="mt-8 text-sm text-gray-400">
                    <p>📞 Contact us: 0912-345-6789</p>
                    <p class="mt-1">📍 Poblacion, Bilar, Bohol</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
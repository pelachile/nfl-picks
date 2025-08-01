<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - NFL Picks App</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">NFL Picks Dashboard</h1>
            <p class="mt-2 text-gray-600">Welcome back, {{ auth()->user()->name }}!</p>
        </div>

        <!-- Main Content -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-6 py-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Ready to make your picks?</h2>
                <p class="text-gray-600 mb-8">Current week's games, group management, and leaderboards coming soon!</p>

                <!-- Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                        View Current Week
                    </button>
                    <button class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                        My Groups
                    </button>
                    <button class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                        Leaderboard
                    </button>
                </div>

                <!-- User Info & Logout -->
                <div class="border-t pt-6 flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Logged in as</p>
                        <p class="font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

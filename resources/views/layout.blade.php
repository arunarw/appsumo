<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock AppSumo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-blue-600 text-white p-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold">Mock AppSumo</a>
            <a href="{{ route('buy') }}" class="bg-white text-blue-600 px-4 py-2 rounded font-semibold hover:bg-blue-50">Buy License</a>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto py-8 px-4">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>

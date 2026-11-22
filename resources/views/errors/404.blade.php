<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>404 - blikk :: shortener</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-[#f7f5ef] to-[#eae8e1] text-slate-900 antialiased flex flex-col">
    <header class="border-b border-slate-200 bg-[#FF9900]">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-5 sm:px-8">
            <a href="/" class="flex items-center gap-3 font-semibold tracking-tight hover:opacity-80 transition">
                <img src="{{ asset('blikk_gross.png') }}" alt="blikk.it" class="h-8 w-auto">
                <span class="text-xl">blikk :: shortener</span>
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-2xl px-5 py-16 sm:px-8 sm:py-24 flex-grow flex items-center justify-center">
        <div class="text-center">
            <!-- Error Code -->
            <div class="mb-8">
                <div class="text-9xl font-bold text-[#FF9900] opacity-20 mb-4">404</div>
                <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 mb-4">
                    {{ __('shortener.error_404_title') }}
                </h1>
            </div>

            <!-- Error Description -->
            <div class="mb-8 space-y-4">
                <p class="text-lg text-slate-600 leading-relaxed">
                    {{ __('shortener.error_404_message') }}
                </p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 inline-block">
                    <p class="text-sm text-red-700 font-mono">{{ request()->path() }}</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mt-10">
                <a href="/" class="inline-flex items-center justify-center px-6 py-3 bg-[#FF9900] text-white font-semibold rounded-lg hover:bg-orange-500 transition shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12a9 9 0 1 1 18 0 9 9 0 0 1-18 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                    </svg>
                    {{ __('shortener.create_new_link') }}
                </a>
                @guest
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-slate-200 text-slate-900 font-semibold rounded-lg hover:bg-slate-300 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2m14-4V7a2 2 0 00-2-2H9a2 2 0 00-2 2v2" />
                        </svg>
                        {{ __('shortener.login') }}
                    </a>
                @else
                    <a href="{{ route('shortener.dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-slate-200 text-slate-900 font-semibold rounded-lg hover:bg-slate-300 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 10h16" />
                        </svg>
                        {{ __('shortener.dashboard') }}
                    </a>
                @endguest
            </div>

            <!-- Help Section -->
            <div class="mt-12 p-6 bg-blue-50 border border-blue-200 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">{{ __('shortener.error_help_title') }}</h3>
                <ul class="text-sm text-blue-700 space-y-2">
                    <li>✓ {{ __('shortener.error_check_url') }}</li>
                    <li>✓ {{ __('shortener.error_check_link_exists') }}</li>
                    <li>✓ {{ __('shortener.error_check_link_published') }}</li>
                </ul>
            </div>
        </div>
    </main>

    <footer class="py-8 text-center text-sm bg-[#FF9900] text-slate-900">
        <p>{{ __('shortener.footer') }}</p>
        <div class="mt-2">
            <p>&copy; <a class="text-red-700 hover:text-red-900" href="https://www.blikk.it">Bildungsserver blikk</a>
                <span>{{ __('shortener.department_prefix') }}
                    <a class="text-red-700 hover:text-red-900" href="https://deutsche-bildung.provinz.bz.it/de/didaktik-beratung">{{ __('shortener.education_department') }}</a>
                    - {{ __('shortener.city') }} {{ date('Y') }}
                </span>
            </p>
        </div>
    </footer>
</body>
</html>

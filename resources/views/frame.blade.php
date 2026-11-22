<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta property="og:image" content="https://www.blikk.it/blikk_og_image.png">

    <title>blikk :: shortener</title>

    @php($translations = __('shortener'))
    <script>window.translations = {{ Illuminate\Support\Js::from($translations) }};</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body class="min-h-screen bg-[#f7f5ef] text-slate-900 antialiased flex flex-col">
    <header class="border-b border-slate-200 bg-[#FF9900]">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-5 sm:px-8">
            <a href="https://www.blikk.it" class="flex items-center gap-3 font-semibold tracking-tight">
                <img src="{{ asset('blikk_gross.png') }}" alt="blikk.it" class="h-8 w-auto">
                <span class="text-xl">blikk :: shortener</span>
            </a>
            <div class="flex flex-wrap gap-4 items-center justify-end">
                @guest
                    <p class="text-sm">{{ __('shortener.tagline') }}</p>
                @endguest
                @include('language-selector')
                @auth
                    <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold">
                            {{ __('shortener.logout') }}
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8 sm:py-12 flex-grow">
        @yield('main')
    </main>

    <footer class="py-8 text-center text-sm bg-[#FF9900]">
        <p>{{ __('shortener.footer') }}</p>
        <div>
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

@extends('frame')

@section('main')
<div class="text-center">
    <!-- Error Code -->
    <div class="mb-8">
        <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 mb-4">
            {{ __('shortener.error_410_title') }}
        </h1>
    </div>

    <!-- Error Description -->
    <div class="mb-8 space-y-4">
        <p class="text-lg text-slate-600 leading-relaxed">
            {{ __('shortener.error_410_message') }}
        </p>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 inline-block">
            <p class="text-sm text-red-700 font-mono">{{ request()->path() }}</p>
        </div>
    </div>

    <!-- Expired State Icon -->
    <div class="mb-8 flex justify-center">
        <div class="inline-block">
            <svg class="w-16 h-16 text-red-500 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <!-- Status Information -->
    <div class="mb-8 p-6 bg-red-50 border border-red-200 rounded-lg">
        <h3 class="text-lg font-semibold text-red-900 mb-3">{{ __('shortener.error_410_expired') }}</h3>
        <p class="text-red-700 text-sm leading-relaxed mb-4">
            {{ __('shortener.error_410_no_longer_active') }}
        </p>
        <div class="text-red-700 text-sm font-mono bg-white bg-opacity-50 p-3 rounded">
            <span class="font-semibold">📅 {{ __('shortener.error_410_permanently') }}</span>
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
            <li>✓ {{ __('shortener.error_410_link_expired_text') }}</li>
            <li>✓ {{ __('shortener.error_410_ask_creator') }}</li>
            <li>✓ {{ __('shortener.error_410_create_yourself') }}</li>
        </ul>
    </div>
</div>
@endsection

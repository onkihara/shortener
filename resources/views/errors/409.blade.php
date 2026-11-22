@extends('frame')

@section('main')
<div class="text-center">
    <!-- Error Code -->
    <div class="mb-8">
        <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 mb-4">
            {{ __('shortener.error_409_title') }}
        </h1>
    </div>

    <!-- Error Description -->
    <div class="mb-8 space-y-4">
        <p class="text-lg text-slate-600 leading-relaxed">
            {{ __('shortener.error_409_message') }}
        </p>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 inline-block">
            <p class="text-sm text-yellow-700 font-mono">{{ request()->path() }}</p>
        </div>
    </div>

    <!-- Waiting State Animation -->
    <div class="mb-8 flex justify-center">
        <div class="inline-block">
            <svg class="w-16 h-16 text-yellow-500 opacity-50 animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    <!-- Status Information -->
    <div class="mb-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
        <h3 class="text-lg font-semibold text-yellow-900 mb-3">{{ __('shortener.error_409_scheduled') }}</h3>
        <p class="text-yellow-700 text-sm leading-relaxed mb-4">
            {{ __('shortener.error_409_wait_message') }}
        </p>
        <div class="text-yellow-700 text-sm font-mono bg-white bg-opacity-50 p-3 rounded">
            <span class="font-semibold">⏰ {{ __('shortener.error_409_check_later') }}</span>
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
        <button onclick="window.location.reload()" class="inline-flex items-center justify-center px-6 py-3 bg-slate-200 text-slate-900 font-semibold rounded-lg hover:bg-slate-300 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ __('shortener.retry') }}
        </button>
    </div>

    <!-- Help Section -->
    <div class="mt-12 p-6 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-900 mb-2">{{ __('shortener.error_help_title') }}</h3>
        <ul class="text-sm text-blue-700 space-y-2">
            <li>✓ {{ __('shortener.error_409_scheduled_link') }}</li>
            <li>✓ {{ __('shortener.error_409_check_time') }}</li>
            <li>✓ {{ __('shortener.error_409_contact_creator') }}</li>
        </ul>
    </div>
</div>
@endsection
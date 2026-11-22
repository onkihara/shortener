@extends('frame')

@section('main')
<div
    id="heroApp"
    data-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
    data-login-route="{{ route('login') }}"
    data-dashboard-route="{{ route('shortener.dashboard') }}"
    data-scroll-target="#createFormApp"
></div>
@vite('resources/js/mounts/heroMount.js')

<div class="py-16 md:py-24 bg-gray-50 dark:bg-gray-900">
    <div
        id="createFormApp"
        data-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
        data-hash-pattern="{{ config('shortener.hash.allowed') }}"
        data-login-route="{{ route('login') }}"
        data-dashboard-route="{{ route('shortener.dashboard') }}"
    ></div>
    @vite('resources/js/mounts/createFormMount.js')
</div>
@endsection
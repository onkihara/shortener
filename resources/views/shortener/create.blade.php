@extends('frame')

@section('main')
<div
    id="createFormApp"
    data-authenticated="{{ auth()->check() ? 'true' : 'false' }}"
    data-hash-pattern="{{ config('shortener.hash.allowed') }}"
    data-login-route="{{ route('login') }}"
    data-dashboard-route="{{ route('shortener.dashboard') }}"
></div>
@vite('resources/js/mounts/createFormMount.js')
@endsection
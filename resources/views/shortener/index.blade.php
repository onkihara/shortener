@extends('frame')

@section('main')
<div
    id="heroApp"
    data-authenticated="false"
    data-login-route="{{ route('login') }}"
    data-dashboard-route="{{ route('shortener.dashboard') }}"
    data-create-route="{{ route('shortener.create-form') }}"
></div>
@vite('resources/js/mounts/heroMount.js')
@endsection

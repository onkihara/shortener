@extends('frame')

@section('main')
<div 
    id="dashboardApp" 
    data-timezone="{{ config('app.timezone') }}"
    data-create-route="{{ route('shortener.create-form') }}"
></div>
@vite('resources/js/mounts/dashboardMount.js')
@endsection

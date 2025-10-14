<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('bootstrap/font/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    @include('welcome-partials.navbar')
    @include('welcome-partials.section-hero')
    @include('welcome-partials.section-fitur')
    @include('welcome-partials.footer')
    <script src="{{ asset('bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>

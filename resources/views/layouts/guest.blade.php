<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/css/auth.css', 'resources/js/app.js'])
</head>

<body>

<div class="card">

    <div class="logo-wrap">
        <a href="/">
            <x-application-logo class="w-8 h-8 object-contain" />
        </a>
    </div>

    <div class="heading">
        <h1>Bem-vindo de volta</h1>
        <p>Faça login para continuar</p>
    </div>

    <div class="divider"></div>

    {{ $slot }}

</div>

</body>
</html>
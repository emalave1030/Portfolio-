<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $profile ? $profile['name'].' — '.$profile['title'] : config('app.name') }}</title>
    @if ($profile)
        <meta name="description" content="{{ $profile['name'] }} — {{ $profile['title'] }}, {{ $profile['location'] }}.">
        <link rel="canonical" href="{{ $profile['site_url'] }}">
    @endif
    <meta name="theme-color" content="#f3ead7">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#about">Skip to content</a>
    <div id="app"></div>
</body>
</html>

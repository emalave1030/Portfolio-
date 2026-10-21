<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>{{ $profile ? $profile['name'] : config('app.name') }}</title>
    @if ($profile)
        <meta name="description" content="{{ $profile['name'] }} — {{ $profile['title'] }}, {{ $profile['location'] }}.">
        <link rel="canonical" href="{{ $profile['site_url'] }}">

        {{-- The site name Google shows above the result title. --}}
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $profile['name'],
                'url' => $profile['site_url'],
            ], JSON_UNESCAPED_SLASHES) !!}
        </script>

        {{-- Link previews in messages and social apps: the wordmark card, not a page screenshot. --}}
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $profile['family_name'] }}">
        <meta property="og:description" content="{{ $profile['name'] }} — {{ $profile['title'] }}, {{ $profile['location'] }}.">
        <meta property="og:url" content="{{ $profile['site_url'] }}">
        <meta property="og:image" content="{{ asset('og-image.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $profile['family_name'] }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif
    <meta name="theme-color" content="#f3ead7">
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#about">Skip to content</a>
    <div id="app"></div>
</body>
</html>

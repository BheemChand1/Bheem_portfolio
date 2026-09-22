<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $profile['seo_title'] ?? 'Bheem Chand')</title>
    <meta name="description" content="@yield('description', $profile['seo_description'] ?? '')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', $profile['seo_title'] ?? 'Bheem Chand')">
    <meta property="og:description" content="@yield('description', $profile['seo_description'] ?? '')">
    <meta property="og:type" content="@yield('og_type', 'website')"><meta property="og:url" content="{{ url()->current() }}">
    @if(View::hasSection('og_image'))<meta property="og:image" content="@yield('og_image')">@elseif(!empty($profile['og_image']))<meta property="og:image" content="{{ asset('storage/'.$profile['og_image']) }}">@endif
    <meta name="twitter:card" content="summary_large_image"><meta name="theme-color" content="#131b23">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <script>try{document.documentElement.dataset.theme=localStorage.getItem('theme')||(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light')}catch(e){}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php($person = ['@context' => 'https://schema.org', '@type' => 'Person', 'name' => $profile['name'] ?? 'Bheem Chand', 'jobTitle' => $profile['title'] ?? '', 'url' => url('/'), 'sameAs' => collect(['linkedin', 'github'])->filter(fn($key) => ($profile['show_'.$key] ?? false) && !empty($profile[$key]))->map(fn($key) => $profile[$key])->values()->all()])
    <script type="application/ld+json">{!! json_encode($person, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) !!}</script>
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
<header class="site-header wrap">
    <a class="brand" href="/" aria-label="{{ $profile['name'] ?? 'Bheem Chand' }} home"><span class="brand-symbol">b<span>.</span></span><span>{{ $profile['name'] ?? 'Bheem Chand' }}<small>{{ $profile['title'] ?? 'Full Stack Developer' }}</small></span></a>
    <button class="menu-toggle icon-button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navigation">☰</button>
    <nav id="navigation" aria-label="Main navigation">
        @foreach(['/' => 'Home', '/about' => 'About', '/projects' => 'Projects', '/experience' => 'Experience'] as $url => $label)<a href="{{ $url }}" @if(request()->getPathInfo() === $url) aria-current="page" @endif>{{ $label }}</a>@endforeach
        @if($profile['blog_enabled'] ?? false)<a href="/articles">Articles</a>@endif
        <a class="nav-contact" href="/contact">Let’s talk <span aria-hidden="true">↗</span></a>
    </nav>
    <button class="theme-toggle icon-button" aria-label="Switch color theme" title="Switch color theme">◐</button>
</header>
<main id="main">@yield('content')</main>
<footer class="wrap site-footer"><div><a class="footer-name" href="/">{{ $profile['name'] ?? 'Bheem Chand' }}<span>.</span></a><p>© {{ date('Y') }} · Built with Laravel & care.</p></div><div class="footer-links"><a href="/skills">Skills</a><a href="/resume">Résumé</a><a href="/contact">Contact ↗</a>@foreach(['github' => 'GitHub', 'linkedin' => 'LinkedIn'] as $key => $label)@if(($profile['show_'.$key] ?? false) && !empty($profile[$key]))<a href="{{ $profile[$key] }}" target="_blank" rel="noopener noreferrer">{{ $label }} ↗</a>@endif @endforeach</div><a href="#" class="back-top">Back to top ↑</a></footer>
@if($profile['chat_enabled'] ?? false)@include('partials.chat')@endif
</body></html>

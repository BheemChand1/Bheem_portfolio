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
    @php($person = ['@context' => 'https://schema.org', '@type' => 'Person', 'name' => $profile['name'] ?? 'Bheem Chand', 'jobTitle' => $profile['title'] ?? '', 'url' => url('/'), 'sameAs' => collect(['linkedin', 'github', 'twitter'])->filter(fn($key) => ($profile['show_'.$key] ?? false) && !empty($profile[$key]))->map(fn($key) => $profile[$key])->values()->all()])
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
<footer class="wrap site-footer">
    <div><a class="footer-name" href="/">{{ $profile['name'] ?? 'Bheem Chand' }}<span>.</span></a><p>© {{ date('Y') }} · Built with Laravel & care.</p></div>
    <div class="footer-links"><a href="/skills">Skills</a><a href="/resume">Résumé</a><a href="/contact">Contact ↗</a></div>
    <nav class="social-links" aria-label="Social media links">
        @if(($profile['show_linkedin'] ?? false) && !empty($profile['linkedin']))
            <a class="social-link social-linkedin" href="{{ $profile['linkedin'] }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn profile" title="LinkedIn">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28ZM5.34 7.43a2.06 2.06 0 1 1 0-4.12 2.06 2.06 0 0 1 0 4.12ZM7.12 20.45H3.56V9h3.56v11.45Z"/></svg>
            </a>
        @endif
        @if(($profile['show_twitter'] ?? false) && !empty($profile['twitter']))
            <a class="social-link social-twitter" href="{{ $profile['twitter'] }}" target="_blank" rel="noopener noreferrer" aria-label="Twitter profile" title="Twitter">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.95 4.57c-.89.39-1.83.65-2.83.77a4.93 4.93 0 0 0 2.17-2.72 9.86 9.86 0 0 1-3.13 1.2 4.92 4.92 0 0 0-8.52 3.37c0 .39.04.76.13 1.12A13.98 13.98 0 0 1 1.64 3.16a4.9 4.9 0 0 0 1.52 6.57 4.9 4.9 0 0 1-2.23-.62v.06a4.93 4.93 0 0 0 3.95 4.83 4.96 4.96 0 0 1-2.22.08 4.93 4.93 0 0 0 4.6 3.42A9.87 9.87 0 0 1 0 19.54a13.94 13.94 0 0 0 7.55 2.21c9.06 0 14.01-7.5 14.01-14.01l-.01-.64a10 10 0 0 0 2.4-2.53Z"/></svg>
            </a>
        @endif
        @if(($profile['show_github'] ?? false) && !empty($profile['github']))
            <a class="social-link social-github" href="{{ $profile['github'] }}" target="_blank" rel="noopener noreferrer" aria-label="GitHub profile" title="GitHub">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 .5a12 12 0 0 0-3.79 23.39c.6.11.82-.26.82-.58v-2.23c-3.34.73-4.04-1.42-4.04-1.42-.55-1.39-1.34-1.76-1.34-1.76-1.09-.75.08-.73.08-.73 1.21.08 1.84 1.24 1.84 1.24 1.07 1.84 2.81 1.31 3.5 1 .11-.78.42-1.31.76-1.61-2.67-.3-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.13-.3-.54-1.52.11-3.18 0 0 1.01-.32 3.3 1.23A11.5 11.5 0 0 1 12 6.3c1.02 0 2.04.14 3 .4 2.29-1.55 3.29-1.23 3.29-1.23.65 1.66.24 2.88.12 3.18a4.65 4.65 0 0 1 1.23 3.22c0 4.61-2.81 5.62-5.48 5.92.43.37.81 1.1.81 2.22v3.3c0 .32.22.7.83.58A12 12 0 0 0 12 .5Z"/></svg>
            </a>
        @endif
        @if(($profile['show_email'] ?? false) && !empty($profile['email']))
            <a class="social-link social-gmail" href="mailto:{{ $profile['email'] }}" aria-label="Email {{ $profile['email'] }}" title="Gmail">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285f4" d="M3.5 18.5h3.75V9.4L2.4 5.76v11.47c0 .7.49 1.27 1.1 1.27Z"/><path fill="#34a853" d="M16.75 18.5h3.75c.61 0 1.1-.57 1.1-1.27V5.76L16.75 9.4v9.1Z"/><path fill="#fbbc04" d="M16.75 5.67V9.4l4.85-3.64v-.93c0-1.56-1.55-2.45-2.66-1.52l-2.19 1.64v.72Z"/><path fill="#ea4335" d="M7.25 9.4V5.67L12 9.23l4.75-3.56V9.4L12 12.96 7.25 9.4Z"/><path fill="#c5221f" d="M2.4 4.83v.93L7.25 9.4V5.67L5.06 4.03C3.95 3.2 2.4 4.08 2.4 4.83Z"/></svg>
            </a>
        @endif
    </nav>
    <a href="#" class="back-top">Back to top ↑</a>
</footer>
@if($profile['chat_enabled'] ?? false)@include('partials.chat')@endif
</body></html>

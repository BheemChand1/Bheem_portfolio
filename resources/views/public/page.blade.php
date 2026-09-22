@extends('layouts.public')
@if($page !== 'home')@section('title', ucfirst($page).' — '.($profile['name'] ?? 'Bheem Chand'))@endif
@section('content')
@if($page === 'home')
<section class="hero hero-modern wrap">
    <div class="hero-copy">
        <div class="availability-pill"><span></span>{{ $profile['title'] }} · {{ $profile['show_location'] ?? false ? $profile['location'] : 'Full stack development' }}</div>
        <div class="eyebrow"><span class="tiny-line"></span> {{ $copy['hero_eyebrow'] }}</div>
        <h1><span class="hero-name">Hi, I’m {{ $profile['name'] }}.</span>{{ $profile['hero_heading'] }}</h1>
        <p class="hero-intro">{{ $profile['hero_intro'] }}</p>
        <div class="hero-actions"><a class="button primary" href="/projects">Explore selected work <span>↗</span></a><a class="button ghost-button" href="/resume">View résumé <span>↓</span></a></div>
        @include('partials.hero-socials')
        <div class="hero-proof" aria-label="Career highlights">
            <div><strong>3+</strong><span>years building<br>web applications</span></div>
            <div><strong>40%</strong><span>API response-time<br>improvement</span></div>
            <div><strong>02</strong><span>enterprise systems<br>for Indian Railways</span></div>
        </div>
    </div>
    <div class="hero-visual portrait-stage spotlight-card">
        <div class="aurora aurora-one"></div><div class="aurora aurora-two"></div><div class="visual-grid"></div>
        <div class="portrait-frame">
            @if(!empty($profile['image']))
                <img src="{{ asset('storage/'.$profile['image']) }}" alt="{{ $profile['name'] }}, {{ $profile['title'] }}" width="900" height="1050" fetchpriority="high">
            @else
                <div class="hero-photo-placeholder"><strong>BC</strong><span>Add your profile image in Admin → Profile & settings</span></div>
            @endif
            <div class="portrait-shade"></div>
            <div class="portrait-caption"><span>Currently at</span><strong>Beatle Analytics</strong><small>Full Stack Developer · Dehradun</small></div>
        </div>
        <div class="floating-tag top-tag glass-chip"><span class="green-dot"></span> Laravel · React · MySQL</div>
        <div class="floating-tag bottom-tag glass-chip"><span class="stack-icon">⌘</span><div>{{ $copy['hero_visual_heading'] }}<small>{{ $copy['hero_visual_caption'] }}</small></div><span class="tag-arrow">↗</span></div>
        <span class="visual-caption">&lt; SECURE · SCALABLE · USER-FOCUSED /&gt;</span>
    </div>
</section>
<div class="stack-strip"><div class="wrap"><span class="stack-label">MY EVERYDAY STACK</span>@foreach(array_filter(array_map('trim', explode(',', $copy['stack']))) as $tech)<span class="stack-item"><span aria-hidden="true">{{ ['Laravel'=>'◇', 'React.js'=>'◎', 'PHP'=>'⌘', 'MySQL'=>'▱', 'Tailwind CSS'=>'≈'][$tech] ?? '◇' }}</span>{{ $tech }}</span>@endforeach</div></div>
@endif

@if(in_array($page, ['home', 'projects']))
<section class="section wrap"><div class="section-heading"><div><div class="eyebrow">01 / SELECTED WORK</div><h2>{{ $profile['projects_heading'] }}</h2><p>{{ $copy['projects_intro'] }}</p></div>@if($page === 'home')<a class="text-link" href="/projects">All projects ↗</a>@endif</div>
@if($page === 'projects')<nav class="filters" aria-label="Filter projects"><a class="{{ !$filter ? 'active' : '' }}" href="/projects">All work</a>@foreach($tags as $tag)<a class="{{ $filter === $tag ? 'active' : '' }}" href="{{ url('/projects').'?'.http_build_query(['tag' => $tag]) }}">{{ $tag }}</a>@endforeach</nav>@endif
<div class="project-grid">@forelse($contents->get('project', collect())->when($page === 'home', fn($items) => $items->where('featured', true)->take(2)) as $project)@include('partials.project')@empty<p>No published projects match this selection.</p>@endforelse</div></section>
@endif

@if(in_array($page, ['home', 'about']))
<section class="section about-section"><div class="wrap about-grid"><div><div class="eyebrow">02 / THE DEVELOPER BEHIND THE CODE</div><h2>{{ $copy['about_heading'] }}</h2>@if(!empty($profile['image']))<img class="portrait" src="{{ asset('storage/'.$profile['image']) }}" alt="{{ $profile['name'] }}" width="480" height="480" loading="lazy">@else<div class="monogram" aria-label="Profile image placeholder">BC<span>PHOTO COMING SOON</span></div>@endif</div><div class="about-copy"><p class="lead">{{ $profile['bio'] }}</p>@if($page === 'about')<p class="prose">{{ $profile['story'] }}</p>@else<a class="text-link" href="/about">A bit more about me ↗</a>@endif<div class="about-notes"><span>BACKEND TO FRONTEND</span><p>{{ $copy['about_focus'] }}</p></div><a class="button outline" href="/experience">Explore my experience ↗</a></div></div></section>
@endif

@if(in_array($page, ['home', 'skills']))
<section class="section wrap"><div class="section-heading"><div><div class="eyebrow">03 / TOOLS OF THE TRADE</div><h2>{{ $copy['skills_heading'] }}</h2></div><p class="heading-note">{{ $copy['skills_intro'] }}</p></div><div class="skill-grid">@foreach($contents->get('skill', collect()) as $skill)<article class="skill-card spotlight-card"><span class="skill-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} /</span><h3>{{ $skill->title }}</h3><div class="tags">@foreach(explode(',', $skill->body) as $s)<span>{{ trim($s) }}</span>@endforeach</div></article>@endforeach</div></section>
@endif

@if($page === 'experience')
<section class="section wrap"><div class="eyebrow">THE JOURNEY SO FAR</div><h1 class="page-title">{{ $copy['experience_heading'] }}</h1><p class="page-lead">{{ $copy['experience_intro'] }}</p>@foreach(['experience' => 'Work experience', 'education' => 'Education'] as $type => $label)<div class="timeline-section"><h2>{{ $label }}</h2><div class="timeline">@foreach($contents->get($type, collect()) as $entry)<article class="timeline-item"><div class="timeline-date">{{ $entry->data['period'] ?? '' }}</div><div><span class="eyebrow">{{ $entry->data['organization'] ?? '' }}</span><h3>{{ $entry->title }}</h3><span class="muted">{{ $entry->data['location'] ?? '' }}</span><p>{{ $entry->body }}</p></div></article>@endforeach</div></div>@endforeach</section>
@endif

@if($page === 'resume')
<section class="section wrap"><div class="eyebrow">EXPERIENCE, ON PAPER</div><h1 class="page-title">{{ $copy['resume_heading'] }}</h1><p class="page-lead">{{ $copy['resume_intro'] }}</p>@if(($profile['resume_enabled'] ?? false) && \App\Models\Setting::get('resume'))<a class="button primary" href="/resume/file?download=1">Download résumé ↓</a><p class="muted resume-note">PDF document · Opens in your browser or PDF reader.</p><iframe class="resume-preview" src="/resume/file" title="Bheem Chand’s résumé PDF"></iframe><p><a class="text-link" href="/resume/file" target="_blank" rel="noopener">Open the PDF in a new tab ↗</a></p>@else<p class="notice">The résumé PDF is not currently available. <a href="/contact">Contact me for a copy.</a></p>@endif</section>
@endif

@if($page === 'contact')
<section class="section wrap"><div class="eyebrow">START A CONVERSATION</div><div class="contact-grid"><div><h1 class="page-title">{{ $profile['contact_heading'] }}</h1><p class="page-lead">{{ $copy['contact_intro'] }}</p><div class="contact-details">@if($profile['show_email'] ?? false)<span>DROP ME A LINE</span><a href="mailto:{{ $profile['email'] }}">{{ $profile['email'] }} ↗</a>@endif @if($profile['show_location'] ?? false)<span>BASED IN</span><p>{{ $profile['location'] }}</p>@endif @if(($profile['show_phone'] ?? false) && $profile['phone'])<span>PHONE</span><a href="tel:{{ preg_replace('/[^+0-9]/', '', $profile['phone']) }}">{{ $profile['phone'] }}</a>@endif @if(!empty($profile['availability']))<span>AVAILABILITY</span><p>{{ $profile['availability'] }}</p>@endif</div></div><form class="contact-form panel" method="post" action="/contact">@csrf @include('partials.messages')<label for="name">Your name</label><input id="name" name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email" maxlength="255"><label for="message">What would you like to build?</label><textarea id="message" name="message" rows="6" minlength="20" maxlength="5000" required placeholder="A little about your project or question…">{{ old('message') }}</textarea><div class="honeypot" aria-hidden="true"><label for="website">Leave this empty</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div><p class="form-note">Your details are used only to respond to your message and are stored in a private inbox.</p><button class="button primary" type="submit">Send message <span>↗</span></button></form></div></section>
@endif

@if($page === 'articles')
<section class="section wrap"><div class="eyebrow">NOTES FROM THE WORKBENCH</div><h1 class="page-title">{{ $copy['articles_heading'] }}</h1><div class="project-grid">@forelse($contents->get('post', collect()) as $post)<article class="panel"><span class="eyebrow">{{ $post->created_at->format('M d, Y') }}</span><h2><a href="/articles/{{ $post->slug }}">{{ $post->title }}</a></h2><p>{{ \Illuminate\Support\Str::limit($post->body, 200) }}</p><a class="text-link" href="/articles/{{ $post->slug }}">Read article ↗</a></article>@empty<p class="notice">No articles published yet. Check back soon.</p>@endforelse</div></section>
@endif

@if($page !== 'contact')<section class="wrap cta-section"><div><span class="eyebrow">HAVE SOMETHING IN MIND?</span><h2>{{ $profile['contact_heading'] }}</h2></div><a class="button primary" href="/contact">Let’s talk <span>↗</span></a></section>@endif
@endsection

<article class="project-card spotlight-card">
    <a class="project-art art-{{ $loop->index % 2 }}" href="/projects/{{ $project->slug }}" aria-label="View {{ $project->title }}">
    @if($project->image)<img src="{{ asset('storage/'.$project->image) }}" alt="{{ $project->data['image_alt'] ?? $project->title }}" loading="lazy" width="900" height="600">
    @else
        <div class="architecture" aria-hidden="true"><div class="arch-label">{{ $project->data['organization'] ?? 'PROJECT' }} / SYSTEM OVERVIEW</div><div class="diagram-node main-node">{{ $project->data['short_title'] ?? $project->title }}<span>APPLICATION LAYER</span></div><div class="diagram-line"></div><div class="diagram-branches"><div>Interface<span>WEB</span></div><div>REST API<span>SERVICES</span></div><div>MySQL<span>DATA</span></div></div></div>
        <span class="placeholder-note">Concept illustration · screenshot coming soon</span>
    @endif
    <span class="project-open" aria-hidden="true">↗</span></a>
    <div class="project-meta"><span>{{ $project->data['category'] ?? 'Project' }}</span><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>
    <h3><a href="/projects/{{ $project->slug }}">{{ $project->title }}</a></h3><p>{{ \Illuminate\Support\Str::limit($project->body, 145) }}</p>
    <div class="tags">@foreach(array_slice(explode(',', $project->data['tags'] ?? ''), 0, 4) as $tag)<span>{{ trim($tag) }}</span>@endforeach</div>
</article>

<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    public function page(Request $request, string $page = 'home')
    {
        abort_unless(in_array($page, ['home', 'about', 'experience', 'skills', 'projects', 'resume', 'contact', 'articles']), 404);
        $profile = Setting::get('profile', []);
        if ($page === 'articles') {
            abort_unless($profile['blog_enabled'] ?? false, 404);
        }
        $contents = Content::published()->whereIn('type', ['project', 'skill', 'experience', 'education', 'post'])->get()->groupBy('type');
        $tags = $contents->get('project', collect())->flatMap(fn ($p) => array_map('trim', explode(',', $p->data['tags'] ?? '')))->filter()->unique()->values();
        $filter = $request->string('tag')->toString();
        if ($filter) {
            $contents->put('project', $contents->get('project', collect())->filter(fn ($p) => in_array($filter, array_map('trim', explode(',', $p->data['tags'] ?? '')))));
        }
        if ($page === 'contact') {
            $request->session()->put('contact_started', time());
        }

        return view('public.page', compact('page', 'profile', 'contents', 'tags', 'filter'));
    }

    public function detail(string $slug)
    {
        $item = Content::published()->where('slug', $slug)->where('type', request()->is('articles/*') ? 'post' : 'project')->firstOrFail();
        if ($item->type === 'post') {
            abort_unless(Setting::get('profile')['blog_enabled'] ?? false, 404);
        }

        return view('public.detail', compact('item'));
    }

    public function contact(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'email' => 'required|email|max:255', 'message' => 'required|string|min:20|max:5000', 'website' => 'nullable|string|max:255']);
        if (! empty($data['website'])) {
            return back()->with('status', 'Thank you. Your message has been received.');
        }
        $started = $request->session()->get('contact_started', 0);
        if (! $started || time() - $started < 3) {
            return back()->withErrors(['message' => 'Please take a moment before submitting.'])->withInput();
        }
        DB::table('submissions')->insert(['name' => $data['name'], 'email' => $data['email'], 'message' => $data['message'], 'created_at' => now(), 'updated_at' => now()]);
        $request->session()->forget('contact_started');

        return back()->with('status', 'Thank you. Your message has been received. I’ll get back to you by email.');
    }

    public function resume(Request $request)
    {
        abort_unless(Setting::get('profile')['resume_enabled'] ?? false, 404);
        $path = Setting::get('resume');
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, 'Bheem-Chand-Resume.pdf', ['Content-Type' => 'application/pdf'], $request->boolean('download') ? 'attachment' : 'inline');
    }

    public function sitemap()
    {
        $urls = collect(['/', '/about', '/experience', '/skills', '/projects', '/resume', '/contact'])->map(fn ($p) => url($p));
        $urls = $urls->merge(Content::published()->where('type', 'project')->get()->map(fn ($p) => url('/projects/'.$p->slug)));
        if (Setting::get('profile')['blog_enabled'] ?? false) {
            $urls = $urls->push(url('/articles'))->merge(Content::published()->where('type', 'post')->get()->map(fn ($p) => url('/articles/'.$p->slug)));
        }

        return response()->view('public.sitemap', compact('urls'))->header('Content-Type', 'application/xml');
    }
}

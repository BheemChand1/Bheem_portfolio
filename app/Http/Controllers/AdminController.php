<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Setting;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public const TYPES = ['project', 'experience', 'education', 'skill', 'post', 'knowledge'];

    public function dashboard()
    {
        return view('admin.dashboard', ['counts' => Content::selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type'), 'unread' => DB::table('submissions')->where('read', false)->count()]);
    }

    public function index(string $type)
    {
        abort_unless(in_array($type, self::TYPES), 404);

        return view('admin.index', ['type' => $type, 'items' => Content::where('type', $type)->orderBy('sort_order')->orderBy('id')->paginate(20)]);
    }

    public function edit(string $type, ?Content $content = null)
    {
        abort_unless(in_array($type, self::TYPES) && (! $content?->exists || $content->type === $type), 404);

        return view('admin.edit', ['type' => $type, 'item' => $content ?? new Content(['data' => []])]);
    }

    public function save(Request $request, MediaService $media, string $type, ?Content $content = null)
    {
        abort_unless(in_array($type, self::TYPES) && (! $content?->exists || $content->type === $type), 404);
        $data = $request->validate([
            'title' => 'required|string|max:200', 'slug' => ['required', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', 'max:200', Rule::unique('contents')->ignore($content?->id)],
            'body' => 'nullable|string|max:50000', 'sort_order' => 'required|integer|min:0|max:100000', 'published' => 'sometimes|boolean', 'featured' => 'sometimes|boolean',
            'seo_title' => 'nullable|string|max:200', 'seo_description' => 'nullable|string|max:300',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120|dimensions:max_width=6000,max_height=6000',
            'data' => 'nullable|array:organization,period,location,tags,category,url,github,image_alt,short_title,features',
            'data.organization' => 'nullable|string|max:200', 'data.period' => 'nullable|string|max:100', 'data.location' => 'nullable|string|max:100',
            'data.tags' => 'nullable|string|max:500', 'data.category' => 'nullable|string|max:100',
            'data.url' => 'nullable|url:http,https|max:1000', 'data.github' => 'nullable|url:http,https|max:1000',
            'data.image_alt' => 'nullable|string|max:255', 'data.short_title' => 'nullable|string|max:100', 'data.features' => 'nullable|string|max:5000',
        ]);
        unset($data['image']);
        $oldImage = $content?->image;
        if ($request->hasFile('image')) {
            $data['image'] = $media->image($request->file('image'));
        } elseif ($request->boolean('remove_image')) {
            $data['image'] = null;
        }
        $data['published'] = $request->boolean('published');
        $data['featured'] = $request->boolean('featured');
        $data['type'] = $type;
        if ($content?->exists) {
            $content->update($data);
        } else {
            $content = Content::create($data);
        }
        if (array_key_exists('image', $data) && $oldImage && $oldImage !== $data['image']) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect('/admin/content/'.$type)->with('status', 'Content saved.');
    }

    public function delete(string $type, Content $content)
    {
        abort_unless(in_array($type, self::TYPES) && $content->type === $type, 404);
        if ($content->image) {
            Storage::disk('public')->delete($content->image);
        }
        $content->delete();

        return back()->with('status', 'Content deleted.');
    }

    public function settings()
    {
        return view('admin.settings', ['profile' => Setting::get('profile', [])]);
    }

    public function copy()
    {
        return view('admin.copy', ['copy' => array_replace(config('copy'), Setting::get('copy', []))]);
    }

    public function saveCopy(Request $request)
    {
        $rules = [];
        foreach (array_keys(config('copy')) as $key) {
            $rules[$key] = 'required|string|max:1500';
        }
        Setting::put('copy', $request->validate($rules));

        return back()->with('status', 'Page copy saved.');
    }

    public function saveSettings(Request $request, MediaService $media)
    {
        $rules = [];
        foreach (['name', 'title', 'location', 'hero_heading', 'projects_heading', 'contact_heading', 'seo_title'] as $field) {
            $rules[$field] = 'required|string|max:200';
        }
        foreach (['hero_intro', 'bio', 'story', 'availability', 'seo_description'] as $field) {
            $rules[$field] = 'nullable|string|max:5000';
        }
        $rules += ['email' => 'required|email|max:255', 'phone' => 'nullable|regex:/^[+0-9 ()-]{5,30}$/', 'linkedin' => 'nullable|url:http,https|max:500', 'github' => 'nullable|url:http,https|max:500', 'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 'og_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 'resume' => 'nullable|file|mimes:pdf|max:10240'];
        $data = $request->validate($rules);
        unset($data['image'], $data['og_image'], $data['resume']);
        $current = Setting::get('profile', []);
        foreach (['show_phone', 'show_email', 'show_location', 'show_linkedin', 'show_github', 'blog_enabled', 'chat_enabled', 'resume_enabled'] as $key) {
            $data[$key] = $request->boolean($key);
        }
        foreach (['image', 'og_image'] as $key) {
            if ($request->hasFile($key)) {
                $data[$key] = $media->image($request->file($key));
            } elseif ($request->boolean('remove_'.$key)) {
                $data[$key] = null;
            }
        }
        Setting::put('profile', array_replace($current, $data));
        foreach (['image', 'og_image'] as $key) {
            if (array_key_exists($key, $data) && ! empty($current[$key]) && $current[$key] !== $data[$key]) {
                Storage::disk('public')->delete($current[$key]);
            }
        }
        if ($request->hasFile('resume') || $request->boolean('remove_resume')) {
            $old = Setting::get('resume');
            Setting::put('resume', $request->hasFile('resume') ? $request->file('resume')->store('resumes', 'local') : null);
            if ($old) {
                Storage::disk('local')->delete($old);
            }
        }

        return back()->with('status', 'Settings saved.');
    }

    public function inbox(string $kind = 'submissions')
    {
        abort_unless(in_array($kind, ['submissions', 'chat_feedback', 'chat_logs']), 404);

        return view('admin.inbox', ['kind' => $kind, 'items' => DB::table($kind)->latest()->paginate(20)]);
    }

    public function updateInbox(Request $request, string $kind, int $id)
    {
        abort_unless(in_array($kind, ['submissions', 'chat_feedback', 'chat_logs']), 404);
        if ($request->input('action') === 'read' && $kind === 'submissions') {
            DB::table($kind)->where('id', $id)->update(['read' => true]);
        } else {
            DB::table($kind)->where('id', $id)->delete();
        }

        return back()->with('status', 'Inbox updated.');
    }
}

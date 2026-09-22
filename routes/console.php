<?php

use App\Models\Setting;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

Artisan::command('portfolio:admin {email?}', function () {
    $email = $this->argument('email') ?: $this->ask('Admin email');
    $password = $this->secret('New password (at least 12 characters, including letters and numbers)');
    $validator = Validator::make(compact('email', 'password'), ['email' => 'required|email|unique:users,email', 'password' => ['required', Password::min(12)->letters()->numbers()]]);
    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }
    $user = new User(['name' => 'Bheem Chand', 'email' => $email, 'password' => $password]);
    $user->is_admin = true;
    $user->save();
    $this->info('Administrator created. Sign in at /admin/login.');
})->purpose('Create an administrator using a hidden password prompt');

Artisan::command('portfolio:resume {path}', function () {
    $path = $this->argument('path');
    if (! is_file($path) || mime_content_type($path) !== 'application/pdf' || filesize($path) > 10485760) {
        $this->error('Supply a valid PDF no larger than 10 MB.');

        return 1;
    }
    $target = 'resumes/'.Str::uuid().'.pdf';
    Storage::disk('local')->put($target, file_get_contents($path));
    $old = Setting::get('resume');
    Setting::put('resume', $target);
    if ($old) {
        Storage::disk('local')->delete($old);
    }
    $this->info('Resume imported. The original PDF includes a phone number; its contents are independent of website visibility settings.');
})->purpose('Import a resume PDF into private storage');

Artisan::command('portfolio:photo {path}', function (MediaService $media) {
    $path = realpath($this->argument('path'));
    if (! $path || ! is_file($path)) {
        $this->error('Supply a valid JPEG, PNG, or WebP image path.');

        return 1;
    }
    $file = new UploadedFile($path, basename($path), mime_content_type($path) ?: null, null, true);
    validator(['image' => $file], ['image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240|dimensions:max_width=6000,max_height=6000'])->validate();
    $profile = Setting::get('profile', []);
    $old = $profile['image'] ?? null;
    $profile['image'] = $media->image($file);
    Setting::put('profile', $profile);
    if ($old) {
        Storage::disk('public')->delete($old);
    }
    $this->info('Profile photo imported and optimized. It can now be replaced or removed in Admin → Profile & settings.');
})->purpose('Import and optimize the admin-managed profile photo');

Artisan::command('portfolio:prune-chat', function () {
    $days = max(0, config('portfolio.ai.retention_days'));
    DB::table('chat_logs')->where('created_at', '<=', now()->subDays($days))->delete();
    $this->info('Expired chat transcripts removed.');
});
Schedule::command('portfolio:prune-chat')->hourly();

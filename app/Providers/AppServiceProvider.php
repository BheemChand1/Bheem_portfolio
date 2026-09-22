<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin', fn ($user) => (bool) $user->is_admin);
        View::composer(['public.*', 'partials.chat'], function ($view) {
            $view->with('copy', array_replace(config('copy'), Setting::get('copy', [])));
        });
        View::composer(['layouts.public', 'layouts.admin', 'partials.chat'], function ($view) {
            $view->with('profile', Setting::get('profile', []));
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\imagetable;
use App\Models\Section;
use App\Models\Banner;
use App\Models\User;
use App\Observers\SectionObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        Section::observe(SectionObserver::class);
        User::observe(UserObserver::class);
        View::composer('*', function ($view) {
            $logo = imagetable::select('img_path')->where('table_name', 'logo')->first();
            $favicon = imagetable::select('img_path')->where('table_name', 'favicon')->first();

            $view->with('logo', $logo)->with('favicon', $favicon);
        });
        Blade::if('canAccess', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });
    }
}

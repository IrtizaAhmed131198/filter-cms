<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\imagetable;
use App\Models\Section;
use App\Observers\SectionObserver;
use Illuminate\Support\Facades\View;

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
        View::composer('*', function ($view) {
            $logo = imagetable::select('img_path')->where('table_name', 'logo')->first();
            $favicon = imagetable::select('img_path')->where('table_name', 'favicon')->first();

            $view->with('logo', $logo)->with('favicon', $favicon);
        });
    }
}

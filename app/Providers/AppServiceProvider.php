<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Schema;
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
        // Share the top-level category tree with the storefront layout nav.
        View::composer('layouts.storefront', function ($view) {
            $navCategories = collect();

            if (Schema::hasTable('categories')) {
                $navCategories = Category::roots()
                    ->where('is_active', true)
                    ->with(['children' => fn ($q) => $q->where('is_active', true)])
                    ->get();
            }

            $view->with('navCategories', $navCategories);
        });
    }
}

<?php

namespace App\Providers;

use App\Services\RecipesList;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
//        $this->app->bind(ShoppingList::class, function (Application $app) {
//            return new ShoppingList();
//        });
        $this->app->bind(RecipesList::class, function (Application $app) {
            $ids = $app->request->input('recipes_ids');
            if(!is_array($ids)) {
                throw new \Exception('Wrong input with recipes ids');
            }
            return new RecipesList($ids, $app->request->user()->id);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    }
}

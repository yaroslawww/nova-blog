<?php

namespace OptimistDigital\NovaBlog;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;
use OptimistDigital\NovaBlog\Http\Middleware\Authorize;
use OptimistDigital\NovaBlog\Nova\Category;
use OptimistDigital\NovaBlog\Nova\Post;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-blog');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../config/nova-blog.php' => config_path('nova-blog.php'),
        ], 'config');

        $this->app->booted(function () {
            $this->routes();
        });

        $postResource = config('nova-blog.post_resource') ?: Post::class;
        $categoryResource = config('nova-blog.category_resource') ?: Category::class;

        Nova::resources([
            $postResource,
            $categoryResource
        ]);

        // Custom validation
        Validator::extend('alpha_dash_or_slash', function ($attribute, $value, $parameters, $validator) {
            if (!is_string($value) && !is_numeric($value)) {
                return false;
            }
            if ($value === '/') {
                return true;
            }
            return preg_match('/^[\pL\pM\pN_-]+$/u', $value) > 0;
        }, 'Field must be alphanumeric with dashes or underscores or a single slash.');
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }
        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/nova-blog')
            ->group(__DIR__ . '/../routes/api.php');
    }
}

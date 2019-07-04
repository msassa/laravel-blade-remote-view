<?php

namespace Wehaa\RemoteView;

use Wehaa\RemoteView\RemoteView;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Wehaa\RemoteView\RemoteViewCompiler;

class RemoteViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        Blade::directive(
            'remoteInclude',
            function ($expression) {
                $tmp = str_replace('.', '/', $expression);
                return "<?php echo RemoteView::make({$tmp}, \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>";
            }
        );
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton(RemoteView::class);

        $this->app->alias(RemoteView::class, 'remote-view');

        $this->app->bind(
            RemoteViewCompiler::class,
            function ($app) {
                $cache_path = storage_path('app/wehaa-view-compiler/views/' . str_slug($_SERVER['HTTP_HOST']));
                return new RemoteViewCompiler($app['files'], $cache_path, $app['config']);
            }
        );
    }
}

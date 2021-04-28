<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Providers;

use File;
use Illuminate\Support\ServiceProvider;
use SimKlee\LaravelBakery\Console\Commands\BakeModelCommand;

class LaravelBakeryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            // commands
            $this->commands([
                BakeModelCommand::class,
            ]);

            // config files
            $this->publishes([
                __DIR__ . '/../../config/laravel-bakery.php' => config_path('laravel-bakery.php'),
            ], 'config');
        }
    }

    public function register(): void
    {
        // config files
        $this->mergeConfigFrom(__DIR__ . '/../../config/laravel-bakery.php', 'laravel-bakery');
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function getResourcePath(string $path): string
    {
        $resourcePath = __DIR__ . '/../../resources/';

        if (!is_null($path)) {
            $resourcePath .= $path;
        }

        return $resourcePath;
    }

}

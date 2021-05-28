<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Providers;

use File;
use Illuminate\Support\ServiceProvider;
use SimKlee\LaravelBakery\Console\Commands\BakeModelAPICommand;
use SimKlee\LaravelBakery\Console\Commands\BakeModelCommand;
use SimKlee\LaravelBakery\Console\Commands\BakeModelCRUDCommand;
use SimKlee\LaravelBakery\Console\Commands\Install;

class LaravelBakeryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            // commands
            $this->commands([
                Install::class,
                BakeModelCommand::class,
                BakeModelCRUDCommand::class,
                BakeModelAPICommand::class,
            ]);

            // config files
            $this->publishes([
                __DIR__ . '/../../config/laravel-bakery.php' => config_path('laravel-bakery.php'),
            ], 'config');

            // classes
            $this->publishes([
                __DIR__ . '/../../resources/classes/AbstractModel.php'             => app_path('Models/AbstractModel.php'),
                __DIR__ . '/../../resources/classes/AbstractRepository.php'        => app_path('Models/Repositories/AbstractRepository.php'),
                __DIR__ . '/../../resources/classes/AbstractModelStoreRequest.php' => app_path('Http/Requests/AbstractModelStoreRequest.php'),
            ], 'classes');
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

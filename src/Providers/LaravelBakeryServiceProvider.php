<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Providers;

use File;
use Illuminate\Support\ServiceProvider;
use SimKlee\LaravelBakery\Console\Commands\BakeModel;
use SimKlee\LaravelBakery\Console\Commands\BakeModelAPI;
use SimKlee\LaravelBakery\Console\Commands\BakeModelAPICommand;
use SimKlee\LaravelBakery\Console\Commands\BakeModelCommand;
use SimKlee\LaravelBakery\Console\Commands\BakeModelCRUDCommand;
use SimKlee\LaravelBakery\Console\Commands\BakeModelViews;
use SimKlee\LaravelBakery\Console\Commands\InstallBakery;

class LaravelBakeryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {

            // commands
            $this->commands([
                InstallBakery::class,
                BakeModel::class,
                BakeModelAPI::class,
                BakeModelViews::class,
            ]);

            // config files
            $this->publishes([
                __DIR__ . '/../../config/laravel-bakery.php' => config_path('laravel-bakery.php'),
                __DIR__ . '/../../config/models.php'         => config_path('models.php'),
            ], 'config');

            $this->publishClasses();
            $this->publishViewComponents();
            $this->publishValidationRules();
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

    public static function createDirectories(): array
    {
        $directories = [
            app_path('Models/Repositories'),
            app_path('Models/Traits'),
            app_path('Http/Requests'),
            app_path('View/Components'),
            app_path('Rules'),
            resource_path('views/components'),
        ];

        $output = [];
        collect($directories)->each(function (string $directory) use (&$output) {
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0775, true);
                $output[] = sprintf('Created directory %s.', $directory);
            } else {
                $output[] = sprintf('Directory %s already exists.', $directory);
            }
        });

        return $output;
    }

    private function publishClasses(): void
    {
        $this->publishes([
            __DIR__ . '/../../resources/classes/AbstractModel.php'             => app_path('Models/AbstractModel.php'),
            __DIR__ . '/../../resources/classes/UuidTrait.php'                 => app_path('Models/Traits/UuidTrait.php'),
            __DIR__ . '/../../resources/classes/AbstractRepository.php'        => app_path('Models/Repositories/AbstractRepository.php'),
            __DIR__ . '/../../resources/classes/AbstractModelStoreRequest.php' => app_path('Http/Requests/AbstractModelStoreRequest.php'),
        ], 'classes');
    }

    private function publishViewComponents(): void
    {
        $components = [
            'BooleanSwitch.php'   => 'boolean-switch.blade.php',
            'Datepicker.php'      => 'datepicker.blade.php',
            'Enum.php'            => 'enum.blade.php',
            'LookupSelectbox.php' => 'lookup-selectbox.blade.php',
            'Selectbox.php'       => 'selectbox.blade.php',
            'Slug.php'            => 'slug.blade.php',
            'Textbox.php'         => 'textbox.blade.php',
        ];

        $publish = [];
        foreach ($components as $class => $view) {
            $publish[ __DIR__ . '/../../resources/classes/view_components/' . $class ] = app_path('View/Components/' . $class);
            $publish[ __DIR__ . '/../../resources/views/components/' . $view ]         = resource_path('views/components/' . $view);
        }
        $this->publishes($publish, 'view_components');
    }

    private function publishValidationRules(): void
    {
        $components = [
            'AbstractIntegerRule.php',
            'AbstractRule.php',
            'SignedIntegerRule.php',
            'SignedMediumIntegerRule.php',
            'SignedSmallIntegerRule.php',
            'SignedTinyIntegerRule.php',
            'UnsignedIntegerRule.php',
            'UnsignedMediumIntegerRule.php',
            'UnsignedSmallIntegerRule.php',
            'UnsignedTinyIntegerRule.php',
        ];

        $publish = [];
        foreach ($components as $class) {
            $publish[ __DIR__ . '/../../resources/classes/rules/' . $class ] = app_path('Rules/' . $class);
        }
        $this->publishes($publish, 'rules');
    }
}

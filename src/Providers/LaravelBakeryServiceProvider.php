<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Providers;

use Illuminate\Support\ServiceProvider;
use SimKlee\LaravelBakery\Console\Commands\BakeModelCommand;

class LaravelBakeryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BakeModelCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        //
    }

}

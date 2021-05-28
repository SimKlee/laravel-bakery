<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Console\Command;
use SimKlee\LaravelBakery\Providers\LaravelBakeryServiceProvider;

/**
 * Class Install
 * @package SimKlee\LaravelBakery\Console\Commands
 *
 * @see     https://stillat.com/blog/2016/12/03/custom-command-styles-with-laravel-artisan
 */
class Install extends Command
{
    const OPTION_FORCE = 'force';

    /**
     * @var string
     */
    protected $signature = 'bake:install {--force : Override existing files.}';

    /**
     * @var string
     */
    protected $description = 'Install Laravel Bakery.';

    /**
     * @return int
     */
    public function handle(): int
    {
        $this->publishConfig();
        $this->createDirectories();
        $this->publishClasses();
        $this->publishViewComponents();
        $this->publishValidationRules();

        return 0;
    }

    private function publishConfig(): void
    {
        $this->info('Publishing package config...');
        $arguments = ['--tag' => 'config'];
        if ($this->option(self::OPTION_FORCE)) {
            $arguments['--force'] = true;
        }
        $this->call('vendor:publish', $arguments);
    }

    private function createDirectories(): void
    {
        $this->info(PHP_EOL);
        $this->info('Creating directories...');
        collect(LaravelBakeryServiceProvider::createDirectories())->each(function (string $line) {
            $this->info($line);
        });
    }

    private function publishClasses(): void
    {
        $this->info(PHP_EOL);
        $this->info('Publishing package classes...');
        $arguments = ['--tag' => 'classes'];
        if ($this->option(self::OPTION_FORCE)) {
            $arguments['--force'] = true;
        }
        $this->call('vendor:publish', $arguments);
    }

    private function publishViewComponents(): void
    {
        $this->info(PHP_EOL);
        $this->info('Publishing view components...');
        $arguments = ['--tag' => 'view_components'];
        if ($this->option(self::OPTION_FORCE)) {
            $arguments['--force'] = true;
        }
        $this->call('vendor:publish', $arguments);
    }

    private function publishValidationRules(): void
    {
        $this->info(PHP_EOL);
        $this->info('Publishing validation rules...');
        $arguments = ['--tag' => 'rules'];
        if ($this->option(self::OPTION_FORCE)) {
            $arguments['--force'] = true;
        }
        $this->call('vendor:publish', $arguments);
    }
}

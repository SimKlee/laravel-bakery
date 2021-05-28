<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Console\Command;

/**
 * Class Install
 * @package SimKlee\LaravelBakery\Console\Commands
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
        $this->createDirectories();
        $this->publishClasses();

        return 0;
    }

    private function createDirectories(): void
    {
        $directories = [
            app_path('Models/Repositories'),
            app_path('Http/Requests'),
        ];

        $this->info(PHP_EOL);
        $this->info('Creating directories...');
        collect($directories)->each(function (string $directory) {
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory);
                $this->info(sprintf('Created directory %s.', $directory));
            } else {
                $this->warn(sprintf('Directory %s already exists.', $directory));
            }
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
}

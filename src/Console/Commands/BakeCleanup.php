<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use SplFileInfo;
use Str;

/**
 * Class BakeCleanup
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeCleanup extends AbstractBake
{
    private const OPTION_SAMPLE = 'sample';

    /** @var string */
    protected $signature = 'bake:cleanup {model?}
                                         {--all : Generate all models in config file}
                                         {--config= : Define the config file name (without file extension .php)}
                                         {--force : Override existing files!}';

    /** @var string */
    protected $description = 'Remove the whole model.';

    protected function handleModel(string $model, Carbon $timestamp = null): int
    {
        $this->title(sprintf('Cleanup model "%s" ...', $model));

        $classes = collect([
            app_path(sprintf('Http/Controllers/Api/%sController.php', $model)),
            app_path(sprintf('Http/Controllers/%sController.php', $model)),
            app_path(sprintf('Http/Requests/%sStoreRequest.php', $model)),
            app_path(sprintf('Http/Resources/%sResource.php', $model)),
            app_path(sprintf('Models/%s.php', $model)),
            app_path(sprintf('Models/Repositories/%sRepository.php', $model)),
            base_path(sprintf('database/factories/%sFactory.php', $model)),
            base_path(sprintf('database/seeders/%sSeeder.php', $model)),
        ]);
        $classes->each(function (string $file) {
            if (File::exists($file)) {
                $this->askAndDelete($file);
            } else {
                $this->debug(sprintf('File "%s" was not generated.', $file));
            }
        });

        $this->findAndDeleteMigration($model);
        $this->cleanRoutes($model);

        return 0;
    }

    private function findAndDeleteMigration(string $model)
    {
        $this->title('Searching for migration ...');
        $search = sprintf('create_%s_table', Str::snake($model));
        collect(File::files(base_path('database/migrations/')))->each(function (SplFileInfo $file) use ($search) {
            if (strpos($file->getFilename(), $search) !== false) {
                $this->askAndDelete($file->getPathname());
            }
        });
    }

    private function askAndDelete(string $file): void
    {
        if (!File::exists($file)) {
            return;
        }

        $delete = false;
        if (!$this->option(self::OPTION_FORCE)) {
            $delete = $this->choice(
                    sprintf('Delete "%s"', $file),
                    ['y' => 'yes', 'n' => 'no'],
                    'no'
                ) === 'y';
        } else {
            $delete = true;
        }

        if ($delete && File::delete($file)) {
            $this->info(sprintf('File "%s" deleted!', $file));
        } else {
            $this->error(sprintf('Deleting "%s" failed!', $file));
        }
    }

    private function cleanRoutes(string $model): void
    {
        $this->title('Removing routes ...');
        collect([
            base_path('routes/api.php'),
            base_path('routes/web.php'),
        ])->each(function (string $routeFile) use ($model) {
            try {
                File::put($routeFile, $this->removeModelRoutes($model, $routeFile));
            } catch (FileNotFoundException $e) {
                $this->error($e->getMessage());
            }
        });
    }

    /**
     * @throws FileNotFoundException
     */
    private function removeModelRoutes(string $model, string $routeFile): string
    {
        try {
            $lines = collect(explode(PHP_EOL, File::get($routeFile)));
        } catch (FileNotFoundException $e) {
            throw $e;
        }

        $search  = sprintf('// %s', $model);
        $cleaned = new Collection();
        $found   = false;

        $lines->each(function (string $line) use ($model, &$found, $search, $cleaned) {
            if ($line === $search) {
                $found = true;

                return true;
            }

            if ($found) {
                $found = false;

                return true;
            }

            $cleaned->add($line);
        });

        return $cleaned->implode(PHP_EOL);
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use SimKlee\LaravelBakery\Models\ModelDefinitionsBag;
use SimKlee\LaravelBakery\Stub\ApiControllerWriter;

/**
 * Class BakeModelAPICommand
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeModelAPICommand extends AbstractBakeCommand
{
    private const ARGUMENT_MODEL = 'model';
    private const OPTION_ALL     = 'all';
    private const OPTION_FORCE   = 'force';

    /**
     * @var string
     */
    protected $signature = 'bake:model:api {model?}
                                       {--abstract : Copies a abstract model to your app models folder}
                                       {--all : Generate all models in config file}
                                       {--force}';

    /**
     * @var string
     */
    protected $description = 'Bake a new model api from a config file.';

    /**
     * @return int
     */
    public function handle(): int
    {
        $this->loadConfiguration();
        $this->modelDefinitionsBag = ModelDefinitionsBag::fromConfig($this->configuration);

        $this->createControllerApiDirectoryIfNotExist();

        if ($this->option(self::OPTION_ALL)) {
            collect(array_keys($this->configuration))->each(function (string $model) {
                $this->handleModel($model);
            });

            return 0;
        }

        $model = $this->argument(self::ARGUMENT_MODEL);
        if ($model && !isset($this->configuration[ $model ])) {
            $this->error(sprintf('Model "%s" is not defined in config file "%s"!', $model, $this->configFile));
            $this->showModels();

            return 1;
        }

        if (!$model) {
            $model = $this->askForModel();
        }

        return $this->handleModel($model);
    }

    /**
     * @param string $model
     *
     * @return int
     * @throws FileNotFoundException
     */
    private function handleModel(string $model): int
    {
        $this->info('Processing ' . $model);

        if ($this->writeControllerClass($model)) {
            $this->info(sprintf('Written api controller for "%s" successfully.', $model));
        }

        $this->addResourceRoute($model);

        return 0;
    }

    private function createControllerApiDirectoryIfNotExist(): void
    {
        if (!File::isDirectory(app_path('Http/Controllers/Api'))) {
            File::makeDirectory(app_path('Http/Controllers/Api'));
        }
    }

    /**
     * @param string $model
     *
     * @return bool
     * @throws FileNotFoundException
     */
    private function writeControllerClass(string $model): bool
    {
        $file     = app_path(sprintf('Http/Controllers/Api/%sController.php', $model));
        $override = true;
        if (!$this->option('force') && File::exists($file)) {
            $override = $this->choice(
                    sprintf('ApiController for "%s" already exists. Overwrite?', $model),
                    ['y' => 'yes', 'n' => 'no'],
                    'no'
                ) === 'y';
        }

        if (!$override) {
            $this->warn(sprintf('Writing model "%s" skipped.', $model));

            return false;
        }

        return ApiControllerWriter::fromModelDefinition($this->modelDefinitionsBag->getModelDefinition($model))
                          ->write($file, $override) !== false;
    }

    /**
     * @param string $model
     */
    private function addResourceRoute(string $model): void
    {
        $content = PHP_EOL . '// ' . $model . PHP_EOL;
        $content .= sprintf('Route::resource(\'%s\', App\Http\Controllers\Api\%sController::class, [\'as\' => \'api\']);', Str::snake($model), $model);

        if (File::append(base_path('routes/api.php'), $content) !== false) {
            $this->info('Added resource routes to "routes/api.php".');
        } else {
            $this->error('Adding resource routes to "routes/api.php" failed!');
        }
    }

}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Carbon;
use SimKlee\LaravelBakery\File\ConsoleFileHelper;
use SimKlee\LaravelBakery\Models\ModelDefinition;
use SimKlee\LaravelBakery\Models\ModelDefinitionsBag;
use SimKlee\LaravelBakery\Providers\LaravelBakeryServiceProvider;
use SimKlee\LaravelBakery\Stub\MigrationWriter;
use SimKlee\LaravelBakery\Stub\ModelWriter;
use SimKlee\LaravelBakery\Stub\Stub;
use Str;

/**
 * Class InstallBlogPackage
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeModelCommand extends AbstractBakeCommand
{
    private const ARGUMENT_MODEL  = 'model';
    private const OPTION_ABSTRACT = 'abstract';
    private const OPTION_ALL      = 'all';
    private const OPTION_CONFIG   = 'config';
    private const OPTION_SAMPLE   = 'sample';

    /**
     * @var string
     */
    protected $signature = 'bake:model {model?}
                                       {--abstract : Copies a abstract model to your app models folder}
                                       {--all : Generate all models in config file}
                                       {--config= : Define the config file name (without file extension .php)}
                                       {--sample : Create a sample config file}
                                       {--force}';

    /**
     * @var string
     */
    protected $description = 'Bake a new model from a config file.';

    /**
     * @return int
     */
    public function handleAbstract(): int
    {
        if (File::copy(__DIR__ . '/../../../resources/classes/AbstractModel.php', app_path('Models/AbstractModel.php'))) {
            $this->info('Created app/Models/AbstractModel.php');
        }

        return 0;
    }

    /**
     * @return int
     */
    public function handle(): int
    {
        if ($this->option(self::OPTION_ABSTRACT)) {
            return $this->handleAbstract();
        }

        if ($this->option(self::OPTION_CONFIG)) {
            $file = $this->option(self::OPTION_CONFIG) . '.php';
            if (!$this->option(self::OPTION_SAMPLE) && !File::exists(config_path($file))) {
                $this->error('Given config file does not exists!');

                return 1;
            }
            $this->configFile = $file;
        }

        if ($this->option(self::OPTION_SAMPLE)) {
            return $this->createSample();
        }

        $this->loadConfiguration();

        $this->modelDefinitionsBag = ModelDefinitionsBag::fromConfig($this->configuration);

        $model = $this->argument(self::ARGUMENT_MODEL);
        if ($model && !isset($this->configuration[ $model ])) {
            $this->error(sprintf('Model "%s" is not defined in config file "%s"!', $model, $this->configFile));
            $this->showModels();

            return 1;
        }

        if (!$model) {
            $model = $this->askForModel();
        }

        $modelDefinition = new ModelDefinition($model, $this->configuration[ $model ]['table'], $this->configuration[ $model ]['timestamps']);
        $modelDefinition->addColumnDefinitions($this->configuration[ $model ]['columns']);

        if ($this->writeModelClass($model)) {
            $this->info(sprintf('Written model "%s" successfully.', $model));
        }

        if ($this->writeModelRepositoryClass($model)) {
            $this->info(sprintf('Written model repository for "%s" successfully.', $model));
        }

        if ($this->writeMigrationFile($model)) {
            $this->info(sprintf('Written migration for "%s" successfully.', $model));
        }


        return 0;
    }

    /**
     * @return int
     */
    private function createSample(): int
    {
        $file = config_path($this->configFile);

        try {
            if ($this->fileHelper->put($file, File::get(ConsoleFileHelper::getResourcePath('config_sample.stub')))) {
                $this->info(sprintf('Written config sample into "%s" successfully.', $file));
            } else {
                $this->warn(sprintf('Skipped writing config sample into "%s".', $file));
            }
        } catch (FileNotFoundException $e) {
            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * @param string $model
     *
     * @return bool
     * @throws FileNotFoundException
     */
    private function writeModelClass(string $model): bool
    {
        $file     = app_path(sprintf('Models/%s.php', $model));
        $override = true;
        if (!$this->option('force') && File::exists($file)) {
            $override = $this->choice(
                    sprintf('Model "%s" already exists. Overwrite?', $model),
                    ['y' => 'yes', 'n' => 'no'],
                    'no'
                ) === 'y';
        }

        if (!$override) {
            $this->warn(sprintf('Writing model "%s" skipped.', $model));

            return false;
        }

        return ModelWriter::fromModelDefinition($this->modelDefinitionsBag->getModelDefinition($model))
                          ->write($file, $override) !== false;
    }

    /**
     * @param string $model
     *
     * @return bool
     * @throws FileNotFoundException
     */
    private function writeModelRepositoryClass(string $model): bool
    {
        $path = app_path('Models/Repositories');
        if (!File::isDirectory($path)) {
            File::makeDirectory($path);
            $this->info(sprintf('Created directory %s', $path));
        }

        if (!File::exists(app_path('Models/Repositories/AbstractRepository.php'))) {
            File::copy(LaravelBakeryServiceProvider::getResourcePath('classes/AbstractRepository.php'), app_path('Models/Repositories/AbstractRepository.php'));
            $this->info('Copied abstract repository to app/Models/Repository');
        }

        $file     = sprintf('%s/%sRepository.php', $path, $model);
        $override = true;
        if (!$this->option('force') && File::exists($file)) {
            $override = $this->choice(
                    sprintf('Model repository for "%s" already exists. Overwrite?', $model),
                    ['y' => 'yes', 'n' => 'no'],
                    'no'
                ) === 'y';
        }

        if (!$override) {
            $this->warn(sprintf('Writing model repository for "%s" skipped.', $model));

            return false;
        }

        $stub = new Stub('repository.stub');
        $stub->replace('model', $model);

        return $stub->write($file, $override) !== false;
    }

    /**
     * @param string      $model
     * @param string|null $timestamp
     *
     * @return bool
     * @throws FileNotFoundException
     */
    private function writeMigrationFile(string $model, string $timestamp = null): bool
    {
        if (is_null($timestamp)) {
            $timestamp = Carbon::now()->format('Y_m_d_His');
        }

        $file = base_path(
            sprintf('database/migrations/%s_create_%s_table.php', $timestamp, Str::plural(Str::snake($model)))
        );

        /**
         * @todo
         *
         * check if migration exists
         * - parse through all migration files get T_CLASS bei get_all_tokens() and check class name
         *
         * - rollback specific migration via migrate:rollback --path=migration_file.php
         *      OR
         * - delete old migration and show hint: run artisan:migration:fresh
         */

        return MigrationWriter::fromModelDefinition($this->modelDefinitionsBag->getModelDefinition($model))
                              ->write($file, true) !== false;
    }
}

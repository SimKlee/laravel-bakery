<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Carbon;
use SimKlee\LaravelBakery\File\ConsoleFileHelper;
use SimKlee\LaravelBakery\Generator\AbstractWriter;
use SimKlee\LaravelBakery\Generator\MigrationWriter;
use SimKlee\LaravelBakery\Generator\ModelFactoryWriter;
use SimKlee\LaravelBakery\Generator\ModelRepositoryWriter;
use SimKlee\LaravelBakery\Generator\ModelWriter;
use SimKlee\LaravelBakery\Model\Exceptions\WrongForeignKeyDefinitionException;
use SimKlee\LaravelBakery\Model\ModelDefinition;
use SimKlee\LaravelBakery\Model\ModelDefinitionsBag;
use Str;

/**
 * Class BakeModel
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeModel extends AbstractBake
{
    private const ARGUMENT_MODEL = 'model';
    private const OPTION_ALL     = 'all';
    private const OPTION_FORCE   = 'force';
    private const OPTION_CONFIG  = 'config';
    private const OPTION_SAMPLE  = 'sample';

    /**
     * @var string
     */
    protected $signature = 'bake:model {model?}
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
     * @throws FileNotFoundException
     * @throws WrongForeignKeyDefinitionException
     */
    public function handle(): int
    {
        $this->loadConfiguration();
        $this->modelDefinitionsBag = ModelDefinitionsBag::fromConfig($this->configuration);

        if ($this->option(self::OPTION_ALL)) {
            $timestamp = Carbon::now();
            collect(array_keys($this->configuration))->each(function (string $model) use ($timestamp) {
                $timestamp->addSecond();
                $this->handleModel($model, $timestamp);
            });

            return 0;
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

    private function handleModel(string $model, Carbon $timestamp = null): int
    {
        if (is_null($timestamp)) {
            $timestamp = Carbon::now();
        }

        $this->info('Processing ' . $model);

        $modelDefinition = $this->modelDefinitionsBag->getModelDefinition($model);

        $this->write(
            new ModelWriter($modelDefinition),
            app_path(sprintf('Models/%s.php', $model)),
            'model class'
        );

        $this->write(
            new ModelRepositoryWriter($modelDefinition),
            app_path(sprintf('Models/Repositories/%sRepository.php', $model)),
            'model repository'
        );

        $this->write(
            new ModelFactoryWriter($modelDefinition),
            database_path(sprintf('factories/%sFactory.php', $model)),
            'model factory'
        );

        $this->write(
            new MigrationWriter($modelDefinition),
            base_path(
                sprintf('database/migrations/%s_create_%s_table.php', $timestamp->format('Y_m_d_His'), Str::plural(Str::snake($model)))
            ),
            'model migration'
        );

        return 0;
    }

    private function write(AbstractWriter $writer, string $file, string $type): void
    {
        $override = true;
        if (!$this->option(self::OPTION_FORCE) && File::exists($file)) {
            $override = $this->choice(
                    sprintf('%s "%s" already exists. Overwrite?', $type, $file),
                    ['y' => 'yes', 'n' => 'no'],
                    'no'
                ) === 'y';
        }

        if (!$override) {
            $this->warn(sprintf('Generating %s "%s" skipped.', $type, $file));

            return;
        }

        $written = $writer->write($file, $override);

        if ($written !== false) {
            $this->info(sprintf('Generated %s "%s"', $type, $file));

            return;
        }

        $this->error(sprintf('Generating %s "%s" failed!', $type, $file));
    }
}

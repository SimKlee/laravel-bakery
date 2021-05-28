<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Support\Carbon;
use SimKlee\LaravelBakery\Generator\AbstractWriter;
use SimKlee\LaravelBakery\Generator\MigrationWriter;
use SimKlee\LaravelBakery\Generator\ModelFactoryWriter;
use SimKlee\LaravelBakery\Generator\ModelRepositoryWriter;
use SimKlee\LaravelBakery\Generator\ModelWriter;
use SimKlee\LaravelBakery\Model\Exceptions\WrongForeignKeyDefinitionException;
use Str;

/**
 * Class BakeModel
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeModel extends AbstractBake
{
    private const OPTION_SAMPLE = 'sample';

    /** @var string */
    protected $signature = 'bake:model {model?}
                                       {--all : Generate all models in config file}
                                       {--config= : Define the config file name (without file extension .php)}
                                       {--sample : Create a sample config file}
                                       {--force : Override existing files!}';

    /** @var string */
    protected $description = 'Bake a new model from a config file.';

    /**
     * @return int
     * @throws WrongForeignKeyDefinitionException
     */
    public function handle(): int
    {
        $this->handleConfig();
        $this->loadConfiguration();

        if ($this->option(self::OPTION_ALL)) {
            return $this->handleAll();
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

    private function handleAll(): int
    {
        $timestamp = Carbon::now();
        collect(array_keys($this->configuration))->each(function (string $model) use ($timestamp) {
            $timestamp->addSecond();
            $this->handleModel($model, $timestamp);
        });

        return 0;
    }

    private function handleConfig(): void
    {
        if ($this->option(self::OPTION_CONFIG)) {
            $file = $this->option(self::OPTION_CONFIG) . '.php';
            if (!$this->option(self::OPTION_SAMPLE) && !File::exists(config_path($file))) {
                $this->error('Given config file does not exists!');

                exit(1);
            }
            $this->configFile = $file;
        }
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
        $written = $writer->write($file, $this->override($type, $file));

        if ($written !== false) {
            $this->info(sprintf('Generated %s "%s"', $type, $file));

            return;
        }

        $this->error(sprintf('Generating %s "%s" failed!', $type, $file));
    }
}

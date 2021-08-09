<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use Illuminate\Support\Carbon;
use SimKlee\LaravelBakery\Generator\MigrationWriter;
use SimKlee\LaravelBakery\Generator\ModelFactoryWriter;
use SimKlee\LaravelBakery\Generator\ModelRepositoryWriter;
use SimKlee\LaravelBakery\Generator\ModelWriter;
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
                                       {--force : Override existing files!}
                                       {--sort=key : Sorting of model list: alpha, key}';

    /** @var string */
    protected $description = 'Bake a new model from a config file.';

    protected function handleModel(string $model, Carbon $timestamp = null): int
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
                sprintf('database/migrations/%s_create_%s_table.php', $timestamp->format('Y_m_d_His'), Str::snake($model))
            ),
            'model migration'
        );

        return 0;
    }
}

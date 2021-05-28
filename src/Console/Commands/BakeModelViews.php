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
 * Class BakeModelViews
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeModelViews extends AbstractBake
{
    /** @var string */
    protected $signature = 'bake:model:views {model?}
                                             {--all : Generate all models in config file}
                                             {--config= : Define the config file name (without file extension .php)}
                                             {--force : Override existing files!}';

    /** @var string */
    protected $description = 'Bake a new model views from a config file.';

    protected function handleModel(string $model, Carbon $timestamp = null): int
    {
        $this->info('Processing ' . $model);

        $modelDefinition = $this->modelDefinitionsBag->getModelDefinition($model);

        // @TODO

        return 0;
    }


}

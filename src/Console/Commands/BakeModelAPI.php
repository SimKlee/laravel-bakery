<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Support\Carbon;
use SimKlee\LaravelBakery\Generator\AbstractWriter;
use SimKlee\LaravelBakery\Generator\ApiControllerWriter;
use SimKlee\LaravelBakery\Generator\ModelResourceWriter;
use SimKlee\LaravelBakery\Model\ModelDefinition;
use Str;

/**
 * Class BakeModelAPI
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeModelAPI extends AbstractBake
{
    /** @var string */
    protected $signature = 'bake:model:api {model?}
                                           {--all : Generate all models in config file}
                                           {--config= : Define the config file name (without file extension .php)}
                                           {--force : Override existing files!}';

    /** @var string */
    protected $description = 'Bake a new model api from a config file.';

    private string           $controllerPath;
    private string           $resourcePath;
    private ?ModelDefinition $modelDefinition = null;

    /**
     * BakeModelCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->controllerPath = app_path('Http/Controllers/Api');
        $this->resourcePath   = app_path('Http/Resources');
    }

    protected function handleModel(string $model, Carbon $timestamp = null): int
    {
        $this->info('Processing ' . $model);

        $this->modelDefinition = $this->modelDefinitionsBag->getModelDefinition($model);

        if (!File::isDirectory($this->controllerPath)) {
            File::makeDirectory($this->controllerPath);
        }

        $written = $this->write(
            new ApiControllerWriter($this->modelDefinition),
            $this->getControllerFilename($model),
            'model api controller'
        );

        if ($written === false) {
            $this->error('Failed writing Model API Controller!');

            return 1;
        }

        $written = $this->writeModelResource($model);
        if ($written === false) {
            $this->error('Failed writing Model Resource!');

            return 1;
        }


        $this->addResourceRoute($model);

        return 0;
    }

    private function writeModelResource(string $model): bool
    {
        if (!File::isDirectory($this->resourcePath)) {
            File::makeDirectory($this->resourcePath);
        }

        return $this->write(
            new ModelResourceWriter($this->modelDefinition),
            $this->getResourceFilename($model),
            'model resource'
        );
    }

    private function addResourceRoute(string $model): void
    {
        $content = PHP_EOL . '// ' . $model . PHP_EOL;
        $content .= sprintf(
            'Route::resource(\'%s\', App\Http\Controllers\Api\%sController::class, [\'as\' => \'api\']);',
            Str::plural(Str::snake($model)),
            $model
        );

        $message = (File::append(base_path('routes/api.php'), $content) > 0)
            ? 'Added resource routes to "routes/api.php".'
            : 'Adding resource routes to "routes/api.php" failed!';

        $this->info($message);
    }

    private function getControllerFilename(string $model): string
    {
        return sprintf('%s/%sController.php', $this->controllerPath, $model);
    }

    private function getResourceFilename(string $model): string
    {
        return sprintf('%s/%sResource.php', $this->resourcePath, $model);
    }
}

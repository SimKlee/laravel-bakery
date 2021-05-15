<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Support\Str;
use SimKlee\LaravelBakery\Models\ModelDefinition;
use SimKlee\LaravelBakery\Stub\ControllerWriter;
use SimKlee\LaravelBakery\Stub\EditCRUDWriter;
use SimKlee\LaravelBakery\Stub\IndexCRUDWriter;
use SimKlee\LaravelBakery\Stub\ModelStoreRequestWriter;

/**
 * Class InstallBlogPackage
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeModelCRUDCommand extends AbstractBakeCommand
{
    private const ARGUMENT_MODEL = 'model';
    private const OPTION_ALL     = 'all';
    private const OPTION_CONFIG  = 'config';
    private const OPTION_FORCE   = 'force';

    /**
     * @var string
     */
    protected $signature = 'bake:model:crud {model?}
                                       {--all : Generate all models in config file}
                                       {--config= : Define the config file name (without file extension .php)}
                                       {--force}';

    /**
     * @var string
     */
    protected $description = 'Bake CRUD resources for a model from a config file.';

    /**
     * @var ModelDefinition
     */
    private ModelDefinition $modelDefinition;

    /**
     * @return int
     */
    public function handle(): int
    {
        // @TODO: implement ask for overriding or force-Arg
        // @TODO: config-Arg
        // @TODO: all-Arg

        $this->loadConfiguration();

        $model = $this->argument(self::ARGUMENT_MODEL)
            ? (string) $this->argument(self::ARGUMENT_MODEL)
            : $this->askForModel();

        return $this->handleModel($model);
    }

    /**
     * @param string $model
     *
     * @return int
     */
    private function handleModel(string $model): int
    {
        $this->modelDefinition = $this->modelDefinitionsBag->getModelDefinition($model);

        $this->addResourceRoute($model);
        $this->createController();
        $this->createModelStoreRequest();
        $this->createIndexView();
        $this->createEditView();

        return 0;
    }

    /**
     * @param string $model
     */
    private function addResourceRoute(string $model): void
    {
        $content = PHP_EOL . '// ' . $model . PHP_EOL;
        $content .= sprintf('Route::resource(\'%s\', App\Http\Controllers\%sController::class);', Str::snake($model), $model);

        if (File::append(base_path('routes/web.php'), $content) !== false) {
            $this->info('Added resource routes to "routes/web.php".');
        } else {
            $this->error('Adding resource routes to "routes/web.php" failed!');
        }
    }

    private function createController(): void
    {
        $file  = app_path(sprintf('Http/Controllers/%sController.php', $this->modelDefinition->getModel()));
        $bytes = ControllerWriter::fromModelDefinition($this->modelDefinition)
                                 ->write($file, true);

        if ($bytes !== false) {
            $this->info(sprintf('Added controller: %s [%s bytes]', $file, $bytes));
        } else {
            $this->error(sprintf('Adding controller "%s" failed!', $file));
        }
    }

    private function createIndexView(): void
    {
        $file  = resource_path(sprintf('views/%s/index.blade.php', $this->modelDefinition->getModel(true)));
        $bytes = IndexCRUDWriter::fromModelDefinition($this->modelDefinition)
                                ->write($file, true);

        if ($bytes !== false) {
            $this->info(sprintf('Added view: %s [%s bytes]', $file, $bytes));
        } else {
            $this->error(sprintf('Adding view "%s" failed!', $file));
        }
    }

    private function createEditView(): void
    {
        $file  = resource_path(sprintf('views/%s/edit.blade.php', $this->modelDefinition->getModel(true)));
        $bytes = EditCRUDWriter::fromModelDefinition($this->modelDefinition)
                               ->write($file, true);

        if ($bytes !== false) {
            $this->info(sprintf('Added view: %s [%s bytes]', $file, $bytes));
        } else {
            $this->error(sprintf('Adding view "%s" failed!', $file));
        }
    }

    private function createModelStoreRequest(): void
    {
        if (!File::isDirectory(app_path('Http/Requests'))) {
            File::makeDirectory(app_path('Http/Requests'));
            $this->info(sprintf('Created directory "%s".', app_path('Http/Requests')));
        }

        if (!File::exists(app_path('Http/Requests/AbstractModelStoreRequest.php'))) {
            File::copy(
                __DIR__ . '/../../../resources/classes/AbstractModelStoreRequest.php',
                app_path('Http/Requests/AbstractModelStoreRequest.php')
            );
            $this->info('Copied "AbstractModelStoreRequest".');
        }

        $file  = app_path(sprintf('Http/Requests/%sStoreRequest.php', $this->modelDefinition->getModel()));
        $bytes = ModelStoreRequestWriter::fromModelDefinition($this->modelDefinition)
                                 ->write($file, true);

        if ($bytes !== false) {
            $this->info(sprintf('Added ModelStoreRequest: %s [%s bytes]', $file, $bytes));
        } else {
            $this->error(sprintf('Adding ModelStoreRequest "%s" failed!', $file));
        }
    }

}

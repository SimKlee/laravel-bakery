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
 * Class BakeModelCommand
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
                                       {--force}
                                       {--menu}';

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

        $this->loadConfiguration();

        if ($this->option('menu')) {
            return $this->createMenu();
        }

        if ($this->option(self::OPTION_ALL)) {
            collect(array_keys($this->configuration))->each(function (string $model) {
                $this->handleModel($model);
            });

            return 0;
        }

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
        $this->info('Processing ' . $model);
        $this->modelDefinition = $this->modelDefinitionsBag->getModelDefinition($model);

        $this->addResourceRoute($model);
        $this->createController();
        $this->createModelStoreRequest();
        $this->createIndexView();
        $this->createEditView();

        $this->info('');

        return 0;
    }

    private function createMenu(): int
    {
        File::delete(config_path('menu.php'));

        $items = collect(array_keys($this->configuration))->map(function (string $model) {
            return [
                'label' => $model,
                'href'  => '/' . Str::snake($model),
            ];
        })->toArray();

        return File::put(config_path('menu.php'), '<?php' . PHP_EOL . 'return ' . var_export($items, true) . ';');
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

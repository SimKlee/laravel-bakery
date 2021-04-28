<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Carbon;
use SimKlee\LaravelBakery\File\ConsoleFileHelper;
use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Models\ModelDefinition;
use SimKlee\LaravelBakery\Models\ModelDefinitionsBag;
use SimKlee\LaravelBakery\Providers\LaravelBakeryServiceProvider;
use SimKlee\LaravelBakery\Stub\ModelWriter;
use SimKlee\LaravelBakery\Stub\Stub;
use Str;

/**
 * Class InstallBlogPackage
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeModelCommand extends Command
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
                                       {--sample : Create a sample config file}';

    /**
     * @var string
     */
    protected $description = 'Bake a new model from a config file.';

    /**
     * @var ConsoleFileHelper
     */
    private $fileHelper;

    /**
     * @var string
     */
    private $configFile = 'models.php';

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var ModelDefinitionsBag
     */
    private $modelDefinitionsBag;

    /**
     * BakeModelCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->fileHelper = new ConsoleFileHelper($this);
    }

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
        }

        return 0;
    }

    private function getModels(): array
    {
        return collect(array_keys($this->configuration))->map(function (string $model, int $i) {
            return [$i, $model];
        })->toArray();
    }

    private function showModels(array $models = null): void
    {
        $this->info('List of defined models:');
        $this->table(['#', 'Model'], $this->getModels());
    }

    private function askForModel(): string
    {
        $this->showModels();
        $models = array_keys($this->configuration);
        $i      = (int) $this->ask('Choose a model');

        return $models[ $i ];
    }

    private function loadConfiguration(): void
    {
        $this->configuration = config(substr($this->configFile, 0, -4));
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
        if (File::exists($file)) {
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
        if (File::exists($file)) {
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
     * @param string $model
     */
    private function writeMigrationFile(string $model): bool
    {
        $file = base_path(sprintf(
            'database/migrations/%s_create_%s_table.php',
            Carbon::now()->format('Y_m_d_His'),
            Str::plural(Str::snake($model))
        ));

        $columns = $this->modelDefinitionsBag
            ->getModelDefinition($model)
            ->getColumns()
            ->map(function (Column $column) {
                $mig = '$table->';
                switch ($column->dataType) {
                    case 'integer':
                        $mig .= 'integer';
                        break;
                }
                $mig .= '(';
                $mig .= ')';
                $mig .= ';';

                return $mig;
            })->implode(PHP_EOL);

        $stub = new Stub('migration.stub');
        $stub->replace('model', $model)
             ->replace('models', Str::plural($model))
             ->replace('columns', $columns)
             ->replace('indexes', null)
             ->replace('foreignKeys', null);

        return $stub->write($file, false) !== false;
    }
}

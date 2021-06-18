<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use SimKlee\LaravelBakery\File\ConsoleFileHelper;
use SimKlee\LaravelBakery\Generator\AbstractWriter;
use SimKlee\LaravelBakery\Model\Column\Exceptions\ColumnComponentValidationException;
use SimKlee\LaravelBakery\Model\ModelDefinitionsBag;

/**
 * Class AbstractBake
 * @package SimKlee\LaravelBakery\Console\Commands
 */
abstract class AbstractBake extends Command
{
    protected const ARGUMENT_MODEL = 'model';
    protected const OPTION_ALL     = 'all';
    protected const OPTION_FORCE   = 'force';
    protected const OPTION_CONFIG  = 'config';

    protected ?ConsoleFileHelper   $fileHelper          = null;
    protected string               $configFile          = 'models.php';
    protected array                $configuration       = [];
    protected ?ModelDefinitionsBag $modelDefinitionsBag = null;

    /**
     * BakeModelCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->fileHelper          = new ConsoleFileHelper($this);
    }

    /**
     * @return array
     */
    protected function getModels(): array
    {
        return collect(array_keys($this->configuration))->map(function (string $model, int $i) {
            $modelPublished      = (int) class_exists('\\App\\Models\\' . $model);
            $controllerPublished = (int) class_exists('\\App\\Http\\Controllers\\' . $model . 'Controller');

            return [$i, $model, $modelPublished, $controllerPublished];
        })->sortBy(function (array $data) {
            return $data[1];
        })->toArray();
    }

    /**
     * @param array|null $models
     */
    protected function showModels(array $models = null): void
    {
        $this->info('List of defined models:');
        $this->table(['#', 'Model', 'Published', 'Controller'], $this->getModels());
    }

    /**
     * @return string
     */
    protected function askForModel(): string
    {
        $this->showModels();
        $models = array_keys($this->configuration);
        $i      = (int) $this->ask('Choose a model');

        return $models[ $i ];
    }

    protected function loadConfiguration(): void
    {
        $this->configuration = config(substr($this->configFile, 0, -4));
        try {
            $this->modelDefinitionsBag = ModelDefinitionsBag::fromConfig($this->configuration);
        } catch (ColumnComponentValidationException $e) {
            $this->error($e->getMessage());
            $e->errors->each(function (string $message) {
                $this->error($message);
            });
            throw $e;
        }
    }

    protected function override(string $type, string $file): bool
    {
        $override = true;
        if (!$this->option(self::OPTION_FORCE) && File::exists($file)) {
            $override = $this->choice(
                    sprintf('%s "%s" already exists. Overwrite?', $type, $file),
                    ['y' => 'yes', 'n' => 'no'],
                    'no'
                ) === 'y';
        }

        return $override;
    }

    protected function handleConfig(): void
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

    protected function write(AbstractWriter $writer, string $file, string $type): bool
    {
        $written = $writer->write($file, $this->override($type, $file));

        if ($written !== false) {
            $this->info(sprintf('Generated %s "%s"', $type, $file));

            return true;
        }

        $this->error(sprintf('Generating %s "%s" failed!', $type, $file));
        return false;
    }

    protected function handleAll(): int
    {
        $timestamp = Carbon::now();
        collect(array_keys($this->configuration))->each(function (string $model) use ($timestamp) {
            $timestamp->addSecond();
            $this->handleModel($model, $timestamp);
        });

        return 0;
    }

    /**
     * @throws ColumnComponentValidationException
     */
    public function handle(): int
    {
        $this->handleConfig();
        $this->loadConfiguration();

        if ($this->option(self::OPTION_ALL)) {
            return $this->handleAll();
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

    abstract protected function handleModel(string $model, Carbon $timestamp = null): int;

}

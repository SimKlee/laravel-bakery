<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use Illuminate\Console\Command;
use SimKlee\LaravelBakery\File\ConsoleFileHelper;
use SimKlee\LaravelBakery\Models\ModelDefinitionsBag;

/**
 * Class AbstractBakeCommand
 * @package SimKlee\LaravelBakery\Console\Commands
 */
abstract class AbstractBakeCommand extends Command
{
    /**
     * @var ConsoleFileHelper
     */
    protected $fileHelper;

    /**
     * @var string
     */
    protected $configFile = 'models.php';

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var ModelDefinitionsBag
     */
    protected $modelDefinitionsBag;

    /**
     * BakeModelCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->fileHelper = new ConsoleFileHelper($this);
    }

    /**
     * @return array
     */
    protected function getModels(): array
    {
        return collect(array_keys($this->configuration))->map(function (string $model, int $i) {
            return [$i, $model];
        })->toArray();
    }

    /**
     * @param array|null $models
     */
    protected function showModels(array $models = null): void
    {
        $this->info('List of defined models:');
        $this->table(['#', 'Model'], $this->getModels());
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
        $this->configuration       = config(substr($this->configFile, 0, -4));
        $this->modelDefinitionsBag = ModelDefinitionsBag::fromConfig($this->configuration);
    }
}

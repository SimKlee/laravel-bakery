<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use Illuminate\Console\Command;
use SimKlee\LaravelBakery\File\ConsoleFileHelper;
use SimKlee\LaravelBakery\Model\ModelDefinitionsBag;

/**
 * Class AbstractBake
 * @package SimKlee\LaravelBakery\Console\Commands
 */
abstract class AbstractBake extends Command
{
    protected ConsoleFileHelper   $fileHelper;
    protected string              $configFile    = 'models.php';
    protected array               $configuration = [];
    protected ModelDefinitionsBag $modelDefinitionsBag;

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
            $modelPublished      = (int) class_exists('\\App\\Models\\' . $model);
            $controllerPublished = (int) class_exists('\\App\\Http\\Controllers\\' . $model . 'Controller');

            return [$i, $model, $modelPublished, $controllerPublished];
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
        $this->configuration       = config(substr($this->configFile, 0, -4));
        $this->modelDefinitionsBag = ModelDefinitionsBag::fromConfig($this->configuration);
    }
}
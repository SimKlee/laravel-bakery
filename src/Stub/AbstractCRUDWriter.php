<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use File;
use Illuminate\Support\Str;
use SimKlee\LaravelBakery\Models\ModelDefinition;

/**
 * Class AbstractCRUDWriter
 * @package SimKlee\LaravelBakery\Support
 */
abstract class AbstractCRUDWriter extends Stub
{
    /**
     * @var ModelDefinition
     */
    protected ModelDefinition $modelDefinition;

    /**
     * @param ModelDefinition $modelDefinition
     *
     * @return AbstractCRUDWriter
     */
    abstract public static function fromModelDefinition(ModelDefinition $modelDefinition): AbstractCRUDWriter;

    /**
     * @param ModelDefinition $modelDefinition
     */
    public function setModelDefinition(ModelDefinition $modelDefinition): void
    {
        $this->modelDefinition = $modelDefinition;
    }

    /**
     * @return string
     */
    protected function getViewsPath(): string
    {
        return resource_path('views/' . Str::snake($this->modelDefinition->getModel()));
    }

    /**
     * @return bool
     */
    public function createDirectoryIfNotExist(): bool
    {
        $directory = $this->getViewsPath();
        if (!File::isDirectory($directory)) {
            return File::makeDirectory($directory);
        }

        return false;
    }

    /**
     * @return ModelDefinition
     */
    public function getModelDefinition(): ModelDefinition
    {
        return $this->modelDefinition;
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Views;

use File;
use SimKlee\LaravelBakery\Generator\AbstractWriter;
use Str;

/**
 * Class AbstractViewWriter
 * @package SimKlee\LaravelBakery\Generator\Views
 */
abstract class AbstractViewWriter extends AbstractWriter
{
    protected string $stubFile = 'views/index.blade.stub';

    protected function getViewsPath(): string
    {
        return resource_path('views/' . Str::snake($this->modelDefinition->getModel()));
    }

    protected function createDirectoryIfNotExist(): bool
    {
        $directory = $this->getViewsPath();
        if (!File::isDirectory($directory)) {
            return File::makeDirectory($directory);
        }

        return false;
    }
}

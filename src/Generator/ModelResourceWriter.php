<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Generator\Formatter\ColumnHelper\ColumnIndexes;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ForeignKeys;
use SimKlee\LaravelBakery\Stub\Formatter\Migrations\ColumnDefinitions;
use Str;

/**
 * Class ModelResourceWriter
 * @package SimKlee\LaravelBakery\Generator
 */
class ModelResourceWriter extends AbstractWriter
{
    protected string $stubFile = 'model_resource.stub';

    protected function handleVars(): void
    {
        $this->setVar('Model', $this->modelDefinition->getModel());
    }
}

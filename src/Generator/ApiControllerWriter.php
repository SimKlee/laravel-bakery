<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Generator\Formatter\ColumnHelper\ColumnIndexes;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ForeignKeys;
use SimKlee\LaravelBakery\Stub\Formatter\Migrations\ColumnDefinitions;
use Str;

/**
 * Class ApiControllerWriter
 * @package SimKlee\LaravelBakery\Generator
 */
class ApiControllerWriter extends AbstractWriter
{
    protected string $stubFile = 'api_controller.stub';

    protected function handleVars(): void
    {
        $this->setVar('Model', $this->modelDefinition->getModel());
        $this->setVar('model', $this->modelDefinition->getModel(true));
    }
}

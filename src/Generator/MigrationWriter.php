<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Generator\Formatter\ColumnHelper\ColumnIndexes;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ForeignKeys;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ColumnDefinitions;
use Str;

/**
 * Class MigrationWriter
 * @package SimKlee\LaravelBakery\Generator
 */
class MigrationWriter extends AbstractWriter
{
    protected string $stubFile = 'migration.stub';

    protected function handleVars(): void
    {
        $this->setVar('Model', $this->modelDefinition->getModel());
        $this->setVar('Models', Str::plural($this->modelDefinition->getModel()));
        $this->setVar('columns', (new ColumnDefinitions($this->modelDefinition))->toString());
        $this->setVar('indexes', (new ColumnIndexes($this->modelDefinition))->toString());
        $this->setVar('foreignKeys', (new ForeignKeys($this->modelDefinition))->toString());
    }
}

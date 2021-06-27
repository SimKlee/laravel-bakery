<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Generator\Formatter\ColumnHelper\ColumnIndexes;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ForeignKeys;
use SimKlee\LaravelBakery\Model\Column\Column;
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
        $this->setVar('model', $this->modelDefinition->getModel(true));
        // @TODO: ask for default implementation (parent::toArray($request)) or list all columns
        #$this->setVar('data', $this->getData());
    }

    private function getData(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->map(function (Column $column) {
                return sprintf(
                    "\t\t\t%s::PROPERTY_%s => \$this->%s,",
                    $column->model,
                    Str::upper($column->name),
                    $column->name,
                );
            })
            ->implode(PHP_EOL);
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\Column\ColumnValidationRule;

/**
 * Class ModelFactoryWriter
 * @package SimKlee\LaravelBakery\Generator
 */
class ModelFactoryWriter extends AbstractWriter
{
    protected string $stubFile = 'factory.stub';

    protected function handleVars(): void
    {
        $this->setVar('Model', $this->modelDefinition->getModel());
        $this->setVar('definitions', $this->getDefinitions());
    }

    private function getDefinitions(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->primaryKey === false;
            })
            ->map(function (Column $column) {
                return sprintf("\t\t\t%s => null,", $column->getPropertyString());
            })
            ->implode(PHP_EOL);
    }

}

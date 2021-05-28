<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\Column\ColumnValidationRule;

/**
 * Class ModelStoreRequestWriter
 * @package SimKlee\LaravelBakery\Generator
 */
class ModelStoreRequestWriter extends AbstractWriter
{
    protected string $stubFile = 'mode_store_request.stub';

    protected function handleVars(): void
    {
        $this->setVar('Model', $this->modelDefinition->getModel());
        $this->setVar('rules', $this->getValidationRules());
    }

    private function getValidationRules(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->primaryKey === false;
            })
            ->map(function (Column $column) {
                return (new ColumnValidationRule($column))->getRule();
            })
            ->implode(PHP_EOL);
    }
}

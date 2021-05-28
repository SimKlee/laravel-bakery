<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\ColumnHelper;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Generator\Stub;
use SimKlee\LaravelBakery\Models\Column;

/**
 * Class ColumnIndexes
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ColumnIndexes extends AbstractFormatter
{
    public function toString(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->index || $column->unique;
            })
            ->map(function (Column $column) {
                return $this->getColumnIndexes($column);
            })->implode(PHP_EOL);
    }

    private function getColumnIndexes(Column $column): string
    {
        $type = null;
        if ($column->index) {
            $type = 'index';
        } else if ($column->unique) {
            $type = 'unique';
        }

        $stub = new Stub('column_index_definition.stub');

        return $stub->setVar('index', $type)
            ->setVar('column', $column->getPropertyString())
            ->getContent();
    }
}

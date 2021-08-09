<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Migrations;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Generator\Stub;
use SimKlee\LaravelBakery\Model\Column\Column;

/**
 * Class ColumnIndexes
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ColumnIndexes extends AbstractFormatter
{
    public function toString(): string
    {
        $indexes = $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->index || $column->unique;
            });

        if ($indexes->count() > 0) {
            $header  = PHP_EOL . "\t\t\t" . '// indexes' . PHP_EOL;
            $content = $indexes->map(function (Column $column) {
                return $this->getColumnIndexes($column);
            })->implode(PHP_EOL);

            return $header . $content;
        }

        return '';
    }

    private function getColumnIndexes(Column $column): string
    {
        $type = null;
        if ($column->index) {
            $type = 'index';
        } else {
            if ($column->unique) {
                $type = 'unique';
            }
        }

        $stub = new Stub('migrations/column_index_definition.stub');

        return $stub->setVar('index', $type)
                    ->setVar('column', $column->getPropertyString())
                    ->getContent();
    }
}

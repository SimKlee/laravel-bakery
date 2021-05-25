<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper;

use SimKlee\LaravelBakery\Database\Migration\ColumnHelper;
use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Stub\Formatter\AbstractFormatter;

/**
 * Class ColumnIndexes
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ColumnIndexes extends AbstractFormatter
{
    /**
     * @return string
     */
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

    /**
     * @param Column $column
     *
     * @return string|false
     * @throws \Exception
     */
    private function getColumnIndexes(Column $column)
    {
        $type = null;

        if ($column->index) {
            $type = 'index';
        } else if ($column->unique) {
            $type = 'unique';
        }

        if ($type) {
            return sprintf("\t\t\t\$table->%s([%s], %s);", $type, $column->getPropertyString(), $column->getPropertyString());
        }

        return false;
    }
}

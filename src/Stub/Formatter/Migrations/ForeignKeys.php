<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub\Formatter\Migrations;

use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Stub\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Support\ModelHelper;

/**
 * Class ForeignKeys
 * @package SimKlee\LaravelBakery\Stub\Formatter\Migrations
 */
class ForeignKeys extends AbstractFormatter
{
    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->foreignKey;
            })
            ->map(function (Column $column) {
                return $this->getColumnForeignKey($column);
            })->implode(PHP_EOL);
    }

    /**
     * @param Column $column
     *
     * @return false|string
     */
    private function getColumnForeignKey(Column $column)
    {
        if ($column->foreignKey === false) {
            return false;
        }

        $foreignKeyName = sprintf('fk__%s__%s', ModelHelper::model2Table($column->model), $column->name);
        $foreignKey     = sprintf("\t\t\t\$table->foreign(%s, '%s')\n", $column->getPropertyString(), $foreignKeyName);
        // @TODO: simplify FQN and add model to use
        $foreignKey .= sprintf("\t\t\t\t->on(\App\Models\%s::TABLE)\n", $column->foreignKeyColumn->model);
        $foreignKey .= sprintf("\t\t\t\t->references(\App\Models\%s);", $column->foreignKeyColumn->getPropertyString());

        return $foreignKey;
    }
}

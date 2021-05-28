<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Migrations;

use SimKlee\LaravelBakery\Generator\Stub;
use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Support\ModelHelper;

/**
 * Class ForeignKeys
 * @package SimKlee\LaravelBakery\Stub\Formatter\Migrations
 */
class ForeignKeys extends AbstractFormatter
{
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
     * @return false|string
     */
    private function getColumnForeignKey(Column $column)
    {
        if ($column->foreignKey === false) {
            return false;
        }

        // @TODO: simplify FQN and add model to use
        // @TODO: add onUpdate & onDelete

        $stub = new Stub('foreign_key_definition.stub');

        return $stub->setVar('column', $column->getPropertyString())
                    ->setVar('name', sprintf('fk__%s__%s', ModelHelper::model2Table($column->model), $column->name))
                    ->setVar('ForeignModel', $column->foreignKeyColumn->model)
                    ->setVar('ForeignColumn', $column->foreignKeyColumn->getPropertyString())
                    ->getContent();
    }
}

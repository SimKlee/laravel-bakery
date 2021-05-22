<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

/**
 * Class ColumnIndexes
 * @package SimKlee\LaravelBakery\Model\Column
 */
class ColumnIndexes extends AbstractColumnComponent
{
    const INDEX_PRIMARY_KEY = 'pk';
    const INDEX_FOREIGN_KEY = 'fk';
    const INDEX_INDEX       = 'index';
    const INDEX_UNIQUE      = 'unique';

    protected function parseDefinitions(): void
    {
        $this->column->definitions->each(function (string $item) {
            switch ($item) {
                case self::INDEX_PRIMARY_KEY:
                    $this->column->primaryKey = true;
                    break;

                case self::INDEX_FOREIGN_KEY:
                    $this->column->foreignKey = true;
                    break;

                case self::INDEX_INDEX:
                    $this->column->index = true;
                    break;

                case self::INDEX_UNIQUE:
                    $this->column->unique = true;
                    break;
            }
        });
    }
}

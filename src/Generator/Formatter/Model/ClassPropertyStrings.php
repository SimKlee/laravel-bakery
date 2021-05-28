<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Migrations;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Model\Column\Column;

/**
 * Class ClassPropertyStrings
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ClassPropertyStrings extends AbstractFormatter
{
    public function toString(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf(' * @property %s $%s', $column->phpDataType, $column->name);
        })->implode(PHP_EOL);
    }

}

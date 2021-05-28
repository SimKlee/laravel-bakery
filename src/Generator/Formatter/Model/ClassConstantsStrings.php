<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Migrations;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Model\Column\Column;

/**
 * Class ClassConstantsStrings
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ClassConstantsStrings extends AbstractFormatter
{
    public function toString(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf('    const PROPERTY_%s = \'%s\';', $column->getPropertyString(), $column->name);
        })->implode(PHP_EOL);
    }

}

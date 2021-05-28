<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Migrations;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Model\Column\Column;
use Str;

/**
 * Class ModelDates
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ModelDates extends AbstractFormatter
{
    public function toString(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->phpDataType === 'Carbon';
            })->map(function (Column $column) {
                return sprintf('        self::PROPERTY_%s,', Str::upper($column->name));
            })->implode(PHP_EOL);
    }

}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Model;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\Column\ColumnDataType;
use Str;

/**
 * Class ModelCasts
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ModelCasts extends AbstractFormatter
{
    public function toString(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $this->shouldBeCasted($column);
            })->map(function (Column $column) {
                return sprintf('        self::PROPERTY_%s => \'%s\',', Str::upper($column->name), $column->phpDataType);
            })->implode(PHP_EOL);
    }

    private function shouldBeCasted(Column $column): bool
    {
        if ($column->dataType === ColumnDataType::DATA_TYPE_UUID) {
            return true;
        }

        return !in_array($column->phpDataType, ['string', 'Carbon']);
    }

}

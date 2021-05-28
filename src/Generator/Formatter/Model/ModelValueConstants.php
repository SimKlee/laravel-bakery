<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Migrations;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Model\Column\Column;
use Str;

/**
 * Class ModelValueConstants
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ModelValueConstants extends AbstractFormatter
{
    public function toString(): string
    {
        $valueConstants = [];

        foreach ($this->modelDefinition->values as $column => $values) {
            foreach ($values as $value) {
                $valueConstants[] = sprintf(
                    '%sconst %s_%s = \'%s\';',
                    "\t",
                    Str::upper($column),
                    Str::upper($value),
                    $value
                );
            }
        }

        return implode(PHP_EOL, $valueConstants);
    }

}

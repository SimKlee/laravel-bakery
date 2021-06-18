<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Model;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Model\Column\Column;
use Str;

/**
 * Class ClassConstantsStrings
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ClassConstantsStrings extends AbstractFormatter
{
    public function toString(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf('    public const PROPERTY_%s = \'%s\';', Str::upper($column->name), $column->name);
        })->implode(PHP_EOL);
    }

}

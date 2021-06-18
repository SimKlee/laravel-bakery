<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Model;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;

/**
 * Class ModelTraits
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ModelTraits extends AbstractFormatter
{
    public function toString(): string
    {
        if ($this->modelDefinition->useUuid) {
            return "\tuse UuidTrait;\n";
        }

        return '';
    }

}

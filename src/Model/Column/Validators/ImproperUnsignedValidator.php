<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\Validators;

use SimKlee\LaravelBakery\Model\Column\ColumnDataType;

/**
 * Class ImproperUnsignedValidator
 * @package SimKlee\LaravelBakery\Model\Column\Validators
 */
class ImproperUnsignedValidator extends AbstractValidator
{
    /**
     * @return bool
     */
    public function validate(): bool
    {
        if ($this->column->unsigned && $this->column->phpDataType !== ColumnDataType::PHP_DATA_TYPE_INTEGER) {
            $this->error = new ColumnValidatorError(
                AbstractValidator::LEVEL_WARNING,
                sprintf('Wrong attribute "unsigned" for data type "%s"!', $this->column->dataType)
            );

            $this->column->unsigned = false;
        }

        return true;
    }
}

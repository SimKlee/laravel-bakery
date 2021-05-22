<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\Validators;

use SimKlee\LaravelBakery\Model\Column\ColumnDataType;

/**
 * Class ImproperPrecisionValidator
 * @package SimKlee\LaravelBakery\Model\Column\Validators
 */
class ImproperPrecisionValidator extends AbstractValidator
{
    /**
     * @return bool
     */
    public function validate(): bool
    {
        if ($this->column->precision > 0 && $this->column->dataType !== ColumnDataType::DATA_TYPE_DECIMAL) {
            $this->error      = new ColumnValidatorError(
                AbstractValidator::LEVEL_WARNING,
                sprintf('Precision is %s (> 0) but data type is not decimal!', $this->column->precision)
            );

            $this->column->precision = null;
        }

        return true;
    }
}

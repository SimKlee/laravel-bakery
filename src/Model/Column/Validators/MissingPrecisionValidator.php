<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\Validators;

use SimKlee\LaravelBakery\Model\Column\ColumnDataType;

/**
 * Class MissingPrecisionValidator
 * @package SimKlee\LaravelBakery\Model\Column\Validators
 */
class MissingPrecisionValidator extends AbstractValidator
{
    /**
     * @return bool
     */
    public function validate(): bool
    {
        if ($this->column->precision > 0 && $this->column->dataType !== ColumnDataType::DATA_TYPE_DECIMAL) {
            $this->error      = new ColumnValidatorError(
                AbstractValidator::LEVEL_ERROR,
                'Data type is decimal but no precision is set!'
            );

            return false;
        }

        return true;
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\Validators;

/**
 * Class ImproperNullableValidator
 * @package SimKlee\LaravelBakery\Model\Column\Validators
 */
class ImproperNullableValidator extends AbstractValidator
{
    /**
     * @return bool
     */
    public function validate(): bool
    {
        if ($this->column->nullable && $this->column->autoIncrement) {
            $this->error      = new ColumnValidatorError(
                AbstractValidator::LEVEL_WARNING,
                'Column cannot be nullable for an auto increment!'
            );

            $this->column->nullable = false;
        }

        return true;
    }
}

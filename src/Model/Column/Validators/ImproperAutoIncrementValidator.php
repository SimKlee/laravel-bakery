<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\Validators;

use SimKlee\LaravelBakery\Model\Column\ColumnDataType;

/**
 * Class ImproperAutoIncrementValidator
 * @package SimKlee\LaravelBakery\Model\Column\Validators
 */
class ImproperAutoIncrementValidator extends AbstractValidator
{
    /**
     * @return bool
     */
    public function validate(): bool
    {
        if ($this->column->autoIncrement && $this->column->phpDataType !== ColumnDataType::PHP_DATA_TYPE_INTEGER) {
            $this->error      = new ColumnValidatorError(
                AbstractValidator::LEVEL_WARNING,
                sprintf('Wrong attribute "autoIncrement" for data type "%s"!', $this->column->dataType)
            );

            $this->column->autoIncrement = false;
        }

        return true;
    }
}

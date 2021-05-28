<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\ValidationRules;

use SimKlee\LaravelBakery\Model\Column\ColumnDataType;

/**
 * Class DecimalRule
 * @package SimKlee\LaravelBakery\Model\Column\ValidationRules
 */
class FloatRule extends AbstractRule
{
    public function handle(): void
    {
        if ($this->column->phpDataType === ColumnDataType::PHP_DATA_TYPE_FLOAT) {
            $this->rules->add(sprintf("regex:/^\d*(\.\d{0,%s})?$/", $this->column->precision));
            $this->rules->add('min:0');
            $this->rules->add('max:' . str_repeat('9', $this->column->length));
        }
    }
}

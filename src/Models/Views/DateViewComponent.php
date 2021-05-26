<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models\Views;

/**
 * Class DateViewComponent
 * @package SimKlee\LaravelBakery\Models\Views
 */
class DateViewComponent extends AbstractColumnViewComponent
{
    /**
     * @return string
     */
    public function render(): string
    {
        return sprintf(
            '<x-datepicker id="%s" label="%s"></x-datepicker>',
            $this->column->name,
            $this->column->name
        );
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models\Views;

/**
 * Class EnumViewComponent
 * @package SimKlee\LaravelBakery\Models\Views
 */
class EnumViewComponent extends AbstractColumnViewComponent
{
    /**
     * @return string
     */
    public function render(): string
    {
        return sprintf(
            '<x-enum id="%s" label="%s" :values="%s"></x-enum>',
            $this->column->name,
            $this->column->name,
            $this->column->values
        );
    }
}

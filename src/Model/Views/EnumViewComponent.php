<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Views;

/**
 * Class EnumViewComponent
 * @package SimKlee\LaravelBakery\Model\Views
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

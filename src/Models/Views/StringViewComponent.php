<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models\Views;

/**
 * Class StringViewComponent
 * @package SimKlee\LaravelBakery\Models\Views
 */
class StringViewComponent extends AbstractColumnViewComponent
{
    /**
     * @return string
     */
    public function render(): string
    {
        return sprintf(
            '<x-textbox id="%s" label="%s"></x-textbox>',
            $this->column->name,
            $this->column->name
        );
    }
}

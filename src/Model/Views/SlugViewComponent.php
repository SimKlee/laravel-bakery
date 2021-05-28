<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Views;

use Str;

/**
 * Class SlugViewComponent
 * @package SimKlee\LaravelBakery\Model\Views
 */
class SlugViewComponent extends AbstractColumnViewComponent
{
    /**
     * @return string
     */
    public function render(): string
    {
        // @TODO: make the foreign-id dynamic
        return sprintf(
            '<x-slug id="%s" label="%s" foreign-id="%s"></x-slug>',
            $this->column->name,
            $this->column->name,
            'name',
        );
    }
}

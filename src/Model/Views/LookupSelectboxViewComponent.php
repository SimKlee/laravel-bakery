<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Views;

use Str;

/**
 * Class LookupSelectboxViewComponent
 * @package SimKlee\LaravelBakery\Model\Views
 */
class LookupSelectboxViewComponent extends AbstractColumnViewComponent
{
    /**
     * @return string
     */
    public function render(): string
    {
        return sprintf(
            '<x-lookup-selectbox id="%s" label="%s" :lookup="$%sLookup"></x-lookup-selectbox>',
            $this->column->name,
            $this->column->name,
            Str::camel($this->column->foreignKeyColumn->model)
        );
    }
}

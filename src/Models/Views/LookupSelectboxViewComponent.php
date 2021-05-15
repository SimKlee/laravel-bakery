<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models\Views;

use Str;

/**
 * Class LookupSelectboxViewComponent
 * @package SimKlee\LaravelBakery\Models\Views
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
            Str::snake($this->column->foreignKeyColumn->model)
        );
    }
}

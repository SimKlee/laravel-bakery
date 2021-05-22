<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models\Views;

use SimKlee\LaravelBakery\Models\Column;

abstract class AbstractColumnViewComponent
{
    /**
     * @var Column
     */
    protected Column $column;

    /**
     * AbstractColumnViewComponent constructor.
     *
     * @param Column $column
     */
    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    /**
     * @param Column $column
     *
     * @return AbstractColumnViewComponent
     */
    public static function factory(Column $column): AbstractColumnViewComponent
    {
        if ($column->foreignKey) {
            return new LookupSelectboxViewComponent($column);
        }

        if ($column->name === 'slug') {
            return new SlugViewComponent($column);
        }

        if (is_array($column->values) && count($column->values) > 0) {
            return new EnumViewComponent($column);
        }

        if ($column->dataType === 'varchar') {
            return new StringViewComponent($column);
        }

        return new StringViewComponent($column);
    }

    abstract public function render(): string;
}

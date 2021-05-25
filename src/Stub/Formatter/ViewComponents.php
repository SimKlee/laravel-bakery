<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub\Formatter;

use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Models\Views\AbstractColumnViewComponent;

/**
 * Class ViewComponents
 * @package SimKlee\LaravelBakery\Stub\Formatter
 */
class ViewComponents extends AbstractFormatter
{
    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->primaryKey === false;
            })
            ->map(function (Column $column) {
                return str_repeat("\t", 5) . AbstractColumnViewComponent::factory($column)->render();
            })
            ->implode(PHP_EOL);
    }
}

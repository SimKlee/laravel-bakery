<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use Closure;

/**
 * Interface ColumnParserPipeline
 * @package SimKlee\LaravelBakery\Model\Column
 */
interface ColumnParserPipeline
{
    /**
     * @param Column  $column
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Column $column, Closure $next);
}

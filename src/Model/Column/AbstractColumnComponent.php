<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use Closure;
use SimKlee\LaravelBakery\Model\Column\Exceptions\ColumnComponentValidationException;

/**
 * Class AbstractColumnComponent
 * @package SimKlee\LaravelBakery\Model\Column
 */
abstract class AbstractColumnComponent implements ColumnParserPipeline
{
    protected Column $column;
    protected array  $validationErrors = [];

    /**
     * ColumnAttributes constructor.
     *
     * @param Column $column
     */
    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    /**
     * @param            $componentClass
     * @param Column     $column
     *
     * @return AbstractColumnComponent
     */
    public static function factory($componentClass, Column $column): AbstractColumnComponent
    {
        return new $componentClass($column);
    }

    /**
     * @param Column  $column
     * @param Closure $next
     *
     * @return mixed
     * @throws ColumnComponentValidationException
     */
    public function handle(Column $column, Closure $next)
    {
        $this->parseDefinitions();

        return $next($column);
    }

    /**
     * @return Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }

    abstract protected function parseDefinitions(): void;

}

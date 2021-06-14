<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\Validators;

use SimKlee\LaravelBakery\Model\Column\Column;

/**
 * Class AbstractValidator
 * @package SimKlee\LaravelBakery\Model\Column\Validators
 */
abstract class AbstractValidator
{
    // @TODO: strict mode for warnings

    const LEVEL_ERROR   = 'error';
    const LEVEL_WARNING = 'warning';

    protected Column                $column;
    protected ?ColumnValidatorError $error = null;

    /**
     * AbstractValidator constructor.
     *
     * @param Column $column
     */
    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    /**
     * @param string $class
     * @param Column $column
     *
     * @return AbstractValidator
     */
    public static function factory(string $class, Column $column): AbstractValidator
    {
        return new $class($column);
    }

    /**
     * @return bool
     */
    abstract public function validate(): bool;

    /**
     * @return ColumnValidatorError|null
     */
    public function getError(): ?ColumnValidatorError
    {
        return $this->error;
    }
}

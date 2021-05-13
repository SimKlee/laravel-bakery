<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models;

/**
 * Class Column
 * @package SimKlees\LaravelBakery\Models
 */
class Column
{
    /**
     * @var string
     */
    public $model;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $dataType;

    /**
     * @var string
     */
    public $phpDataType;

    /**
     * @var bool
     */
    public $unsigned = false;

    /**
     * @var bool
     */
    public $primaryKey = false;

    /**
     * @var bool
     */
    public $foreignKey = false;

    /**
     * @var Column|null
     */
    public $foreignKeyColumn = null;

    /**
     * @var string|null
     */
    public $foreignKeyOnUpdate = null;

    /**
     * @var string|null
     */
    public $foreignKeyOnDelete = null;

    /**
     * @var bool
     */
    public $autoIncrement = false;

    /**
     * @var bool
     */
    public $nullable = false;

    /**
     * @var mixed
     */
    public $default;

    /**
     * @var int
     */
    public $length;

    /**
     * @var int
     */
    public $precision;

    /**
     * @var bool
     */
    public $index = false;

    /**
     * @var bool
     */
    public $unique = false;

    /**
     * Column constructor.
     *
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        $this->name = $name;
        // @TODO: model name
    }

    /**
     * @return string
     */
    public function getPropertyString(): string
    {
        return sprintf('%s::PROPERTY_PRODUCT_%s', $this->model, strtoupper($column));
    }
}

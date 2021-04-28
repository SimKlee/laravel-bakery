<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models;

use Str;

/**
 * Class Column
 * @package SimKlees\LaravelBakery\Models
 */
class Column
{
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
    }
}

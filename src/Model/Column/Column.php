<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use SimKlee\LaravelBakery\Support\Collection;

/**
 * Class Column
 * @package SimKlee\LaravelBakery\Model\Column
 */
class Column
{
    public ?string $model;
    public ?string $name;
    public ?string $dataType;
    public ?string $phpDataType;
    public bool    $unsigned      = false;
    public bool    $primaryKey    = false;
    public bool    $autoIncrement = false;
    public bool    $nullable      = false;
    public ?int    $length;
    public ?int    $precision;
    public bool    $index         = false;
    public bool    $unique        = false;
    public bool    $label         = false;
    public array   $values        = [];
    /** @var mixed */
    public             $default;
    public bool        $foreignKey         = false;
    public ?Column     $foreignKeyColumn;
    public ?string     $foreignKeyOnUpdate = null;
    public ?string     $foreignKeyOnDelete = null;
    public Collection $definitions;

    /**
     * Column constructor.
     *
     * @param string     $model
     * @param string     $column
     * @param Collection $definitions
     */
    public function __construct(string $model, string $column, Collection $definitions)
    {
        $this->model       = $model;
        $this->name        = $column;
        $this->definitions = $definitions;
    }

    /**
     * @return string
     */
    public function getPropertyString(): string
    {
        return sprintf('%s::PROPERTY_%s', $this->model, strtoupper($this->name));
    }
}

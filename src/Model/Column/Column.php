<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use SimKlee\LaravelBakery\Support\Collection;

/**
 * Class Column
 * @package SimKlee\LaravelBakery\Model\Column
 */
class Column
{
    public ?string $model         = null;
    public ?string $name          = null;
    public ?string $dataType      = null;
    public ?string $phpDataType   = null;
    public bool    $unsigned      = false;
    public bool    $primaryKey    = false;
    public bool    $autoIncrement = false;
    public bool    $nullable      = false;
    public ?int    $length        = null;
    public ?int    $precision     = null;
    public bool    $index         = false;
    public bool    $unique        = false;
    public bool    $label         = false;
    public array   $values        = [];
    /** @var mixed */
    public            $default;
    public bool       $foreignKey         = false;
    public ?Column    $foreignKeyColumn   = null;
    public ?string    $foreignKeyOnUpdate = 'restrict';
    public ?string    $foreignKeyOnDelete = 'restrict';
    public Collection $definitions;

    /**
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

    public function getPropertyString(): string
    {
        return sprintf('%s::PROPERTY_%s', $this->model, strtoupper($this->name));
    }
}

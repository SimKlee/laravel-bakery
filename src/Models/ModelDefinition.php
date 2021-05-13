<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class ModelDefinition
 * @package SimKlees\LaravelBakery\Models
 */
class ModelDefinition
{
    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $table;

    /**
     * @var bool
     */
    private $timestamps = false;

    /**
     * @var Collection
     */
    private $columnBag;

    /**
     * ModelDefinition constructor.
     *
     * @param string      $model
     * @param string|null $table
     * @param bool        $timestamps
     */
    public function __construct(string $model, string $table = null, bool $timestamps = false)
    {
        $this->model      = $model;
        $this->timestamps = $timestamps;
        $this->table      = (!is_null($table))
            ? $table
            : Str::plural(Str::snake($model));

        $this->columnBag = new Collection();
    }

    /**
     * @param string $model
     *
     * @return ModelDefinition
     */
    public static function fromConfig(string $model): ModelDefinition
    {
        $config = config('models.' . $model);

        return new self($model, $config['table'] ?? null, $config['timestamps'] ?? false);
    }

    /**
     * @param array $definitions
     */
    public function addColumnDefinitions(array $definitions): void
    {
        collect($definitions)->each(function (string $columnDefinition, string $name) {
            $this->addColumn(ColumnParser::parse($name, $columnDefinition));
        });
    }

    /**
     * @param Column $column
     */
    public function addColumn(Column $column): void
    {
        $this->columnBag->add($column);
    }

    /**
     * @return Collection|Column[]
     */
    public function getColumns()
    {
        return $this->columnBag;
    }

    /**
     * @param string $name
     *
     * @return Column|false
     */
    public function getColumn(string $name)
    {
        $filtered = $this->columnBag->filter(function (Column $column) use ($name) {
            return $column->name === $name;
        });

        if ($filtered->count() === 1) {
            return $filtered->first();
        }

        return false;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return bool
     */
    public function hasTimestamps(): bool
    {
        return $this->timestamps;
    }

    /**
     * @return bool
     */
    public function usesCarbon(): bool
    {
        return $this->columnBag->filter(function (Column $column) {
                return $column->phpDataType === 'Carbon';
            })->count() > 0;
    }
}

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
     * @var array
     */
    private $values = [];

    private ?string $label = null;

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
        $config   = config('models.' . $model);
        $instance = new self($model, $config['table'] ?? null, $config['timestamps'] ?? false);
        $instance->addColumnDefinitions($config['columns']);

        if (isset($config['values'])) {
            $instance->setValues($config['values']);
        }

        if (isset($config['label'])) {
            $instance->setLabel($config['label']);
        }

        return $instance;
    }

    /**
     * @param array $definitions
     */
    public function addColumnDefinitions(array $definitions): void
    {
        collect($definitions)->each(function (string $columnDefinition, string $name) {
            $column = ColumnParser::parse($name, $columnDefinition);
            $this->addColumn($column);
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
     * @param bool $snake
     *
     * @return string
     */
    public function getModel(bool $snake = false): string
    {
        if ($snake) {
            return Str::snake($this->model);
        }

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

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }
}

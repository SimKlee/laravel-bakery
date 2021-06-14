<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model;

use Illuminate\Support\Str;
use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\Column\Exceptions\ColumnComponentValidationException;
use SimKlee\LaravelBakery\Support\Collection;
use SimKlee\LaravelBakery\Model\Column\ColumnParser;

/**
 * Class ModelDefinition
 *
 * @package SimKlee\LaravelBakery\Model
 */
class ModelDefinition
{
    public string            $model;
    public bool              $timestamps     = false;
    public bool              $timeRestricted = false;
    public array             $values         = [];
    public string            $table;
    private Collection       $columnBag;
    public ModelRelationsBag $relationBag;

    public function __construct()
    {
        $this->columnBag   = new Collection();
        $this->relationBag = new ModelRelationsBag();
    }

    /**
     * @param string $model
     *
     * @return ModelDefinition
     * @throws ColumnComponentValidationException
     */
    public static function fromConfig(string $model): ModelDefinition
    {
        $config          = config('models.' . $model);
        $instance        = new ModelDefinition();
        $instance->model = $model;

        $instance->timestamps = isset($config['timestamps'])
            ? $config['timestamps']
            : false;

        $instance->table = isset($config['table'])
            ? $config['table']
            : Str::plural(Str::snake($model));

        if (isset($config['values'])) {
            $instance->values = $config['values'];
        }

        $instance->addColumnDefinitions($config['columns']);

        return $instance;
    }

    /**
     * @throws ColumnComponentValidationException
     */
    public function addColumnDefinitions(array $definitions): void
    {
        collect($definitions)->each(function (string $columnDefinition, string $name) {
            $column = ColumnParser::parse($this->model, $name, $columnDefinition);
            if (isset($this->values[ $name ])) {
                $column->values = $this->values[ $name ];
            }
            $this->addColumn($column);
        });
    }

    public function addColumn(Column $column): void
    {
        $this->columnBag->add($column);
    }

    /**
     * @return Collection|Column[]
     */
    public function getColumns(): Collection
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

    public function getModel(bool $snake = false): string
    {
        if ($snake) {
            return Str::snake($this->model);
        }

        return $this->model;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function hasTimestamps(): bool
    {
        return $this->timestamps;
    }

    public function usesCarbon(): bool
    {
        return $this->columnBag->filter(function (Column $column) {
                return $column->phpDataType === 'Carbon';
            })->count() > 0;
    }
}

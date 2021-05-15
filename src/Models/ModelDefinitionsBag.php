<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models;

use Illuminate\Support\Collection;

/**
 * Class ModelDefinitionsBag
 * @package SimKlees\LaravelBakery\Models
 */
class ModelDefinitionsBag
{
    /**
     * @var Collection|ModelDefinition[]
     */
    private $modelBag;

    /**
     * ModelDefinitions constructor.
     */
    public function __construct()
    {
        $this->modelBag = new Collection();
    }

    /**
     * @param array $config
     * @param bool  $foreignKeyLookup
     *
     * @return ModelDefinitionsBag
     */
    public static function fromConfig(array $config, bool $foreignKeyLookup = true): ModelDefinitionsBag
    {
        $bag = new self();
        foreach ($config as $modelName => $settings) {
            $modelDefinition = new ModelDefinition($modelName, $settings['table'] ?? null, $settings['timestamps'] ?? false);
            collect($settings['columns'])->each(function ($definition, $columnName) use ($modelDefinition) {
                // @TODO: find a better way to set the model in the column
                $column        = ColumnParser::parse($columnName, $definition);
                $column->model = $modelDefinition->getModel();
                $modelDefinition->addColumn($column);
            });
            $bag->addModelDefinition($modelDefinition);
        }

        if ($foreignKeyLookup) {
            $bag->lookupForeignKeys();
        }

        return $bag;
    }

    /**
     * @param ModelDefinition $modelDefinition
     */
    public function addModelDefinition(ModelDefinition $modelDefinition): void
    {
        $this->modelBag->add($modelDefinition);
    }

    /**
     * @return Collection|Column[]
     */
    public function getModelDefinitions()
    {
        return $this->modelBag;
    }

    /**
     * @param string $modelName
     *
     * @return ModelDefinition|false
     */
    public function getModelDefinition(string $modelName)
    {
        $filtered = $this->modelBag->filter(function (ModelDefinition $modelDefinition) use ($modelName) {
            return $modelDefinition->getModel() === $modelName;
        });

        if ($filtered->count() === 1) {
            return $filtered->first();
        }

        return false;
    }

    public function lookupForeignKeys(): void
    {
        $this->getModelDefinitions()->each(function (ModelDefinition $modelDefinition) {
            $modelDefinition->getColumns()->each(function (Column $column) {
                if ($column->foreignKey) {
                    $modelDefinition     = $this->getModelDefinition($this->getModelNameFromForeignKey($column->name));
                    $pk                  = $modelDefinition->getColumn('id');
                    $column->dataType    = $pk->dataType;
                    $column->phpDataType = $pk->phpDataType;
                    $column->unsigned    = $pk->unsigned;
                    $column->length      = $pk->length;
                    $column->precision   = $pk->precision;
                    $column->index       = true;

                    $col = $modelDefinition->getColumn('id');

                    $column->foreignKeyColumn = $modelDefinition->getColumn('id');
                }
            });
        });
    }

    /**
     * @param string $columnName
     *
     * @return string
     * @throws \Exception
     */
    public function getModelNameFromForeignKey(string $columnName): string
    {
        $parts = \SimKlee\LaravelBakery\Support\Collection::explode($columnName, '_');

        if ($parts->last() !== 'id') {
            throw new \Exception('A foreign key ends with _id: ' . $columnName);
        }

        $parts->pop();

        return $parts->camel(true);
    }

}

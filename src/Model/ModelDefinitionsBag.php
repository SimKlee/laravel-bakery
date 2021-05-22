<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\Exceptions\WrongForeignKeyDefinitionException;
use SimKlee\LaravelBakery\Support\Collection as BakeryCollection;

/**
 * Class ModelDefinitionsBag
 * @package SimKlee\LaravelBakery\Model
 */
class ModelDefinitionsBag
{
    private Collection $modelDefinitionBag;

    /**
     * ModelDefinitionsBag constructor.
     */
    public function __construct()
    {
        $this->modelDefinitionBag = new Collection();
    }

    /**
     * @param array $config
     * @param bool  $foreignKeyLookup
     *
     * @return ModelDefinitionsBag
     *
     * @throws WrongForeignKeyDefinitionException
     */
    public static function fromConfig(array $config, bool $foreignKeyLookup = true): ModelDefinitionsBag
    {
        $bag = new ModelDefinitionsBag();
        foreach ($config as $modelName => $settings) {
            $bag->addModelDefinitionBag(ModelDefinition::fromConfig($modelName));
        }

        if ($foreignKeyLookup) {
            $bag->lookupForeignKeys();
        }

        return $bag;
    }

    /**
     * @throws WrongForeignKeyDefinitionException
     */
    public function lookupForeignKeys(): void
    {
        $this->modelDefinitionBag->each(function (ModelDefinition $modelDefinition) {
            $modelDefinition
                ->getColumns()
                ->filter(function (Column $column) {
                    return $column->foreignKey;
                })
                ->each(function (Column $column) {
                    $modelDefinition = $this->getModelDefinition($this->getModelNameFromForeignKey($column->name));
                    $pk              = $modelDefinition->getColumn('id');

                    // @TODO: what if not?
                    if ($pk instanceof Column) {
                        $column->dataType    = $pk->dataType;
                        $column->phpDataType = $pk->phpDataType;
                        $column->unsigned    = $pk->unsigned;
                        $column->length      = $pk->length;
                        $column->precision   = $pk->precision;
                        $column->index       = true;

                        $column->foreignKeyColumn = $pk;
                    }
                });
        });
    }

    /**
     * @param string $columnName
     *
     * @return string
     * @throws WrongForeignKeyDefinitionException
     */
    public function getModelNameFromForeignKey(string $columnName): string
    {
        $parts = BakeryCollection::explode($columnName, '_');

        if ($parts->last() !== 'id') {
            throw new WrongForeignKeyDefinitionException(
                sprintf('A foreign key ends with "_id". Given: %s', $columnName)
            );
        }

        $parts->pop();

        return $parts->camel(true);
    }

    /**
     * @param ModelDefinition $modelDefinition
     */
    public function addModelDefinitionBag(ModelDefinition $modelDefinition): void
    {
        $this->modelDefinitionBag->add($modelDefinition);
    }

    /**
     * @return Collection|ModelDefinition[]
     */
    public function getModelDefinitionBag(): Collection
    {
        return $this->modelDefinitionBag;
    }

    /**
     * @return ModelDefinition
     */
    public function getModelDefinition(string $model): ModelDefinition
    {
        return $this->modelDefinitionBag->filter(function (ModelDefinition $modelDefinition) use ($model) {
            return $modelDefinition->model === $model;
        })->first();
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model;

use Illuminate\Support\Collection;

/**
 * Class ModelRelation
 *
 * @package SimKlee\LaravelBakery\Model
 */
class ModelRelationsBag
{
    private array $relations = [];

    public function add(ModelRelation $relation): void
    {
        if (!isset($this->relations[ $relation->model ])) {
            $this->relations[ $relation->model ] = [];
        }

        if (!isset($this->relations[ $relation->targetModel ])) {
            $this->relations[ $relation->targetModel ] = [];
        }

        $this->relations[ $relation->model ][]       = $relation;
        $this->relations[ $relation->targetModel ][] = $relation;
    }

    public function getRelations(string $model = null): array
    {
        if (is_null($model)) {
            return $this->relations;
        }

        if (isset($this->relations[ $model ])) {
            return $this->relations[ $model ];
        }

        return [];
    }
}

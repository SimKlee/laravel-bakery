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
    private Collection $relations;

    public function __construct()
    {
        $this->relations = new Collection();
    }

    public function add(ModelRelation $relation): void
    {
        $this->relations->add($relation);
    }

    public function getRelations(string $model = null): Collection
    {
        if (is_null($model))  {
            return $this->relations;
        }

        return $this->relations->filter(function (ModelRelation $relation) use ($model) {
            return in_array($model, [$relation->model, $relation->targetModel]);
        });
    }
}

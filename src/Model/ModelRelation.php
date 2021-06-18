<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model;

/**
 * Class ModelRelation
 *
 * @package SimKlee\LaravelBakery\Model
 */
class ModelRelation
{
    public const TYPE_ONE_TO_ONE  = 'one_to_one';
    public const TYPE_ONE_TO_MANY = 'one_to_many';

    public const TYPE_BELONGS_TO = 'belongsTo';
    public const TYPE_HAS_MANY   = 'hasMany';

    public ?string $model       = null;
    public ?string $type        = null;
    public ?string $targetModel = null;

    public function __construct(string $model, string $type, string $targetModel)
    {
        $this->model       = $model;
        $this->type        = $type;
        $this->targetModel = $targetModel;
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Model;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Generator\Stub;
use SimKlee\LaravelBakery\Model\ModelRelation;
use Str;

/**
 * Class ModelValueConstants
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ModelRelations extends AbstractFormatter
{
    public function toString(): string
    {
        return $this->modelDefinition->relations->map(function (ModelRelation $modelRelation) {

            switch ($modelRelation->type) {
                case ModelRelation::TYPE_BELONGS_TO:
                    $name = $modelRelation->targetModel;
                    break;
                case ModelRelation::TYPE_HAS_MANY:
                    $name = Str::plural($modelRelation->targetModel);
                    break;
                default:
                    $name = null;
            }

            $stub = new Stub('model_relation.stub');
            $stub->setVar('Model', $modelRelation->targetModel)
                 ->setVar('Type', ucfirst($modelRelation->type))
                 ->setVar('type', $modelRelation->type)
                 ->setVar('name', lcfirst($name));

            return $stub->getContent();

        })->implode(PHP_EOL . PHP_EOL);
    }
}

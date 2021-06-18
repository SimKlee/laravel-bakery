<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Model;

use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\ModelRelation;

/**
 * Class ClassPropertyStrings
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ClassPropertyStrings extends AbstractFormatter
{
    public function toString(): string
    {
        $columnProperties = $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf(' * @property %s $%s', $column->phpDataType, $column->name);
        });
        $columnProperties->prepend(' * Column Properties');

        $relationProperties = $this->modelDefinition->relations->map(function (ModelRelation $modelRelation) {
            if ($modelRelation->type === ModelRelation::TYPE_BELONGS_TO) {
                return sprintf(' * @property %s $%s', $modelRelation->targetModel, lcfirst($modelRelation->targetModel));
            } else if ($modelRelation->type === ModelRelation::TYPE_HAS_MANY) {
                return sprintf(' * @property Collection|%s[] $%s', $modelRelation->targetModel, lcfirst(\Str::plural($modelRelation->targetModel)));
            } else {
                return 'ERROR';
            }
        });

        if ($relationProperties->count() > 0) {
            $relationProperties->prepend(' * Relation Properties');
            $relationProperties->prepend(' *');
        }

        return $columnProperties->merge($relationProperties)
                                ->implode(PHP_EOL);
    }

}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\Column\ColumnValidationRule;

/**
 * Class ModelRepositoryWriter
 * @package SimKlee\LaravelBakery\Generator
 */
class ModelRepositoryWriter extends AbstractWriter
{
    protected string $stubFile = 'repository.stub';

    protected function handleVars(): void
    {
        // @TODO: set label for lookup-method
        $this->setVar('Model', $this->modelDefinition->getModel());
    }
}

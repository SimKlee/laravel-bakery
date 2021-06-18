<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\Column\ColumnValidationRule;
use Str;

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
        if ($this->modelDefinition->label) {
            $this->setVar('lookupMethod', $this->getLookupMethod());
        } else {
            $this->setVar('lookupMethod', '');
        }
    }

    private function getLookupMethod(): string
    {
        $stub = new Stub('repository_lookup_method.stub');
        $stub->setVar('Model', $this->modelDefinition->getModel());
        $stub->setVar('LabelColumn', Str::upper($this->modelDefinition->label));

        return $stub->getContent();
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Generator\Formatter\ColumnHelper\ColumnIndexes;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ForeignKeys;
use SimKlee\LaravelBakery\Stub\Formatter\Migrations\ColumnDefinitions;
use Str;

/**
 * Class ApiControllerWriter
 * @package SimKlee\LaravelBakery\Generator
 */
class ApiControllerWriter extends AbstractWriter
{
    protected string $stubFile = 'api_controller.stub';

    protected function handleVars(): void
    {
        $this->setVar('Model', $this->modelDefinition->getModel());
        $this->setVar('model', $this->modelDefinition->getModel(true));
        $this->setVar('models', Str::plural($this->modelDefinition->getModel(true)));

        $this->setVar('indexMethod', $this->getIndexMethod());
        $this->setVar('showMethod', $this->getShowMethod());
        $this->setVar('storeMethod', $this->getStoreMethod());
        $this->setVar('updateMethod', $this->getUpdateMethod());
        $this->setVar('destroyMethod', $this->getDestroyMethod());
    }

    private function setStubVars(Stub $stub): void
    {
        $stub->setVar('Model', $this->modelDefinition->getModel())
             ->setVar('model', $this->modelDefinition->getModel(true))
             ->setVar('models', Str::plural($this->modelDefinition->getModel(true)));
    }

    private function getIndexMethod(): string
    {
        if ($this->modelDefinition->apiIndex === false) {
            return '';
        }

        $stub = new Stub('api/index.stub');
        $this->setStubVars($stub);

        return $stub->getContent();
    }

    private function getShowMethod(): string
    {
        if ($this->modelDefinition->apiShow === false) {
            return '';
        }

        $stub = new Stub('api/show.stub');
        $this->setStubVars($stub);

        return $stub->getContent();
    }

    private function getStoreMethod(): string
    {
        if ($this->modelDefinition->apiStore === false) {
            return '';
        }

        $stub = new Stub('api/store.stub');
        $this->setStubVars($stub);

        return $stub->getContent();
    }

    private function getUpdateMethod(): string
    {
        if ($this->modelDefinition->apiUpdate === false) {
            return '';
        }

        $stub = new Stub('api/update.stub');
        $this->setStubVars($stub);

        return $stub->getContent();
    }

    private function getDestroyMethod(): string
    {
        if ($this->modelDefinition->apiDestroy === false) {
            return '';
        }

        $stub = new Stub('api/destroy.stub');
        $this->setStubVars($stub);

        return $stub->getContent();
    }
}

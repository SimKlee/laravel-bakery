<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Views;

use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\Views\AbstractColumnViewComponent;

/**
 * Class EditWriter
 * @package SimKlee\LaravelBakery\Generator\Views
 */
class EditWriter extends AbstractViewWriter
{
    protected string $stubFile = 'views/edit.blade.stub';

    protected function handleVars(): void
    {
        $this->createDirectoryIfNotExist();
        $this->setVar('Model', $this->modelDefinition->getModel());
        $this->setVar('model', $this->modelDefinition->getModel(true));
        $this->setVar('components', $this->getColumnViewComponents());
        $this->setVar('layout', config('laravel-bakery.view.layout'));
        $this->setVar('section', config('laravel-bakery.view.section'));
    }

    protected function getColumnViewComponents(): string
    {
        return $this->modelDefinition
                    ->getColumns()
                    ->filter(function (Column $column) {
                        return $column->primaryKey === false;
                    })
                    ->map(function (Column $column) {
                        return str_repeat("\t", 5) . AbstractColumnViewComponent::factory($column)->render();
                    })
                    ->implode(PHP_EOL);
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Views;

use SimKlee\LaravelBakery\Model\Column\Column;
use Str;

/**
 * Class IndexWriter
 * @package SimKlee\LaravelBakery\Generator\Views
 */
class IndexWriter extends AbstractViewWriter
{
    protected string $stubFile = 'views/index.blade.stub';

    protected function handleVars(): void
    {
        $this->createDirectoryIfNotExist();

        $this->setVar('Model', $this->modelDefinition->getModel());
        $this->setVar('Models', Str::plural($this->modelDefinition->getModel()));
        $this->setVar('model', $this->modelDefinition->getModel(true));
        $this->setVar('columnHeaders', $this->getColumnHeader());
        $this->setVar('columnRows', $this->getColumnRows());
        $this->setVar('layout', config('laravel-bakery.view.layout'));
        $this->setVar('section', config('laravel-bakery.view.section'));
    }

    private function getColumnHeader(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf("\t\t\t\t<th>%s</th>", $column->name);
        })->implode(PHP_EOL);
    }

    private function getColumnRows(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf("\t\t\t\t<td>{{ \$result->%s }}</td>", $column->name);
        })->implode(PHP_EOL);
    }
}

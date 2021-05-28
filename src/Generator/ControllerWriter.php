<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Model\Column\Column;
use Str;

/**
 * Class ControllerWriter
 * @package SimKlee\LaravelBakery\Generator
 */
class ControllerWriter extends AbstractWriter
{
    protected string   $stubFile = 'controller.stub';
    private Collection $onlyColumnsWithForeignKeys;

    protected function handleVars(): void
    {
        $this->onlyColumnsWithForeignKeys = $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->foreignKey;
            });

        $this->setVar('Model', $this->modelDefinition->getModel());
        $this->setVar('model', $this->modelDefinition->getModel(true));
        $this->setVar('createRepositories', $this->getCreateRepositories());
        $this->setVar('createWith', $this->getCreateWith());
        $this->setVar('usedModels', $this->getUsedModels());
        $this->setVar('usedRepositories', $this->getUsedRepositories());
    }

    private function getCreateRepositories(): string
    {
        return $this->onlyColumnsWithForeignKeys->map(function (Column $column) {
            return collect([
                sprintf(
                    "\t\t/** @var %sRepository $%sRepository */",
                    $column->foreignKeyColumn->model,
                    Str::camel($column->foreignKeyColumn->model)
                ),
                sprintf(
                    "\t\t$%sRepository = AbstractRepository::create(%s::class);",
                    Str::camel($column->foreignKeyColumn->model),
                    $column->foreignKeyColumn->model
                ),
            ])->implode(PHP_EOL);
        })->implode(PHP_EOL);
    }

    private function getCreateWith(): string
    {
        $string = $this->onlyColumnsWithForeignKeys->map(function (Column $column) {
            return sprintf(
                "\t\t\t->with(\'%sLookup\', $%sRepository->lookup())",
                Str::camel($column->foreignKeyColumn->model),
                Str::camel($column->foreignKeyColumn->model)
            );
        })->implode(PHP_EOL);

        if (!empty($string)) {
            $string = PHP_EOL . $string;
        }

        return $string;
    }

    private function getUsedModels(): string
    {
        return $this->onlyColumnsWithForeignKeys->map(function (Column $column) {
            return sprintf('use App\\Models\\%s;', $column->foreignKeyColumn->model);
        })->implode(PHP_EOL);
    }

    private function getUsedRepositories(): string
    {
        return $this->onlyColumnsWithForeignKeys->map(function (Column $column) {
            return sprintf(
                'use App\\Models\\Repositories\\%sRepository;',
                $column->foreignKeyColumn->model
            );
        })->implode(PHP_EOL);
    }
}

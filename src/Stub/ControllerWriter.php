<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Models\ModelDefinition;
use Str;

/**
 * Class ControllerWriter
 * @package SimKlee\LaravelBakery\Support
 */
class ControllerWriter extends Stub
{
    /**
     * @var ModelDefinition
     */
    private ModelDefinition $modelDefinition;

    /**
     * @var Collection
     */
    private Collection $onlyColumnsWithForeignKeys;

    /**
     *
     * @param ModelDefinition $modelDefinition
     *
     * @return ControllerWriter
     */
    public static function fromModelDefinition(ModelDefinition $modelDefinition): ControllerWriter
    {
        $writer = new ControllerWriter('controller.stub',);
        $writer->setModelDefinition($modelDefinition);

        return $writer;
    }

    /**
     * @param ModelDefinition $modelDefinition
     */
    public function setModelDefinition(ModelDefinition $modelDefinition): void
    {
        $this->modelDefinition = $modelDefinition;
    }

    /**
     * @param string $file
     * @param bool   $override
     *
     * @return bool|int
     */
    public function write(string $file, bool $override = false)
    {
        $this->onlyColumnsWithForeignKeys = $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->foreignKey;
            });

        $this->replace('Model', $this->modelDefinition->getModel())
             ->replace('model', $this->modelDefinition->getModel(true))
             ->replace('createRepositories', $this->getCreateRepositories())
             ->replace('createWith', $this->getCreateWith())
             ->replace('usedModels', $this->getUsedModels())
             ->replace('usedRepositories', $this->getUsedRepositories());

        return parent::write($file, $override);
    }

    /**
     * @return string
     */
    private function getCreateWith(): string
    {
        $string = $this->onlyColumnsWithForeignKeys->map(function (Column $column) {
            return sprintf(
                '%s->with(\'%sLookup\', $%sRepository->lookup())',
                "\t\t\t",
                Str::camel($column->foreignKeyColumn->model),
                Str::camel($column->foreignKeyColumn->model)
            );
        })->implode(PHP_EOL);

        if (!empty($string)) {
            $string = PHP_EOL . $string;
        }

        return $string;
    }

    /**
     * @return string
     */
    private function getUsedModels(): string
    {
        return $this->onlyColumnsWithForeignKeys->map(function (Column $column) {
            return sprintf('use App\\Models\\%s;', $column->foreignKeyColumn->model);
        })->implode(PHP_EOL);
    }

    /**
     * @return string
     */
    private function getUsedRepositories(): string
    {
        return $this->onlyColumnsWithForeignKeys->map(function (Column $column) {
            return sprintf(
                'use App\\Models\\Repositories\\%sRepository;',
                $column->foreignKeyColumn->model
            );
        })->implode(PHP_EOL);
    }

    /**
     * @return string
     */
    private function getCreateRepositories(): string
    {
        return $this->onlyColumnsWithForeignKeys->map(function (Column $column) {
            return collect([
                sprintf(
                    '%s/** @var %sRepository $%sRepository */',
                    "\t\t",
                    $column->foreignKeyColumn->model,
                    Str::camel($column->foreignKeyColumn->model)
                ),
                sprintf(
                    '%s$%sRepository = AbstractRepository::create(%s::class);',
                    "\t\t",
                    Str::camel($column->foreignKeyColumn->model),
                    $column->foreignKeyColumn->model
                ),
            ])->implode(PHP_EOL);
        })->implode(PHP_EOL);
    }
}

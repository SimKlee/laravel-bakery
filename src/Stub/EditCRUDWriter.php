<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Models\ModelDefinition;
use SimKlee\LaravelBakery\Models\Views\AbstractColumnViewComponent;

/**
 * Class EditCRUDWriter
 * @package SimKlee\LaravelBakery\Support
 */
class EditCRUDWriter extends AbstractCRUDWriter
{
    /**
     *
     * @param ModelDefinition $modelDefinition
     *
     * @return AbstractCRUDWriter|EditCRUDWriter
     */
    public static function fromModelDefinition(ModelDefinition $modelDefinition): AbstractCRUDWriter
    {
        $writer = new static('views/edit.blade.stub',);
        $writer->setModelDefinition($modelDefinition);

        return $writer;
    }

    /**
     * @param string $file
     * @param bool   $override
     *
     * @return bool|int
     */
    public function write(string $file, bool $override = false)
    {
        $this->createDirectoryIfNotExist();
        $this->replace('Model', $this->modelDefinition->getModel())
             ->replace('model', $this->modelDefinition->getModel(true))
             ->replace('components', $this->getColumnViewComponents())
             ->replace('layout', config('laravel-bakery.view.layout'))
             ->replace('section', config('laravel-bakery.view.section'));

        parent::write($file, $override);
    }


    /**
     * @return string
     */
    protected function getColumnViewComponents(): string
    {
        return $this->getModelDefinition()
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

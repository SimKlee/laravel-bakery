<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use Illuminate\Support\Str;
use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Models\ModelDefinition;

/**
 * Class IndexCRUDWriter
 * @package SimKlee\LaravelBakery\Support
 */
class IndexCRUDWriter extends AbstractCRUDWriter
{
    /**
     *
     * @param ModelDefinition $modelDefinition
     *
     * @return AbstractCRUDWriter|EditCRUDWriter
     */
    public static function fromModelDefinition(ModelDefinition $modelDefinition): AbstractCRUDWriter
    {
        $writer                  = new static('views/index.blade.stub',);
        $writer->modelDefinition = $modelDefinition;

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
             ->replace('Models', Str::plural($this->modelDefinition->getModel()))
             ->replace('model', $this->modelDefinition->getModel(true))
             ->replace('columnHeaders', $this->getColumnHeader())
             ->replace('columnRows', $this->getColumnRows())
             ->replace('layout', config('laravel-bakery.view.layout'))
             ->replace('section', config('laravel-bakery.view.section'));

        return parent::write($file, $override);
    }

    /**
     * @return string
     */
    private function getColumnHeader(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf("\t\t\t\t<th>%s</th>", $column->name);
        })->implode(PHP_EOL);
    }

    /**
     * @return string
     */
    private function getColumnRows(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf("\t\t\t\t<td>{{ \$result->%s }}</td>", $column->name);
        })->implode(PHP_EOL);
    }

}

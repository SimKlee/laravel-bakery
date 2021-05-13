<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use SimKlee\LaravelBakery\Database\Migration\ColumnHelper;
use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Models\ModelDefinition;
use Str;

/**
 * Class MigrationWriter
 * @package SimKlee\LaravelBakery\Support
 */
class MigrationWriter extends Stub
{
    /**
     * @var ModelDefinition
     */
    private $modelDefinition;

    /**
     *
     * @param ModelDefinition $modelDefinition
     *
     * @return MigrationWriter
     * @throws FileNotFoundException
     */
    public static function fromModelDefinition(ModelDefinition $modelDefinition): MigrationWriter
    {
        $writer = new MigrationWriter('migration.stub');
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
        $this->replace('model', $this->modelDefinition->getModel())
             ->replace('models', Str::plural($this->modelDefinition->getModel()))
             ->replace('columns', $this->getColumnDefinitions())
             ->replace('indexes', $this->getColumnIndexes())
             ->replace('foreignKeys', '');

        return parent::write($file, $override);
    }

    /**
     * @return string
     */
    private function getColumnDefinitions(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            $helper = new ColumnHelper();

            return $helper->getColumnMigration($column);
        })->implode(PHP_EOL);
    }

    /**
     * @return string
     */
    private function getColumnIndexes(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->index || $column->unique;
            })
            ->map(function (Column $column) {
                $helper = new ColumnHelper();

                return $helper->getColumnIndexes($column);
            })->implode(PHP_EOL);
    }

    /**
     * @param ModelDefinition $modelDefinition
     */
    public function setModelDefinition(ModelDefinition $modelDefinition): void
    {
        $this->modelDefinition = $modelDefinition;
    }


}

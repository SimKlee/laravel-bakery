<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Models\ColumnParser;
use SimKlee\LaravelBakery\Models\ModelDefinition;
use Str;

/**
 * Class ModelWriter
 * @package SimKlee\LaravelBakery\Support
 */
class ModelWriter extends Stub
{
    /**
     * @var ModelDefinition
     */
    private $modelDefinition;

    /**
     * @var Collection
     */
    private $uses;

    public function __construct(string $stub)
    {
        parent::__construct($stub);

        $this->uses = new Collection();
    }

    /**
     *
     * @param ModelDefinition $modelDefinition
     *
     * @return ModelWriter
     * @throws FileNotFoundException
     */
    public static function fromModelDefinition(ModelDefinition $modelDefinition): ModelWriter
    {
        $writer = new ModelWriter('model.stub');
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
        $extends = config('laravel-bakery.model.base_model');
        $this->uses->add($extends);

        $this->replace('extends', class_basename($extends))
             ->replace('Model', $this->modelDefinition->getModel())
             ->replace('properties', $this->getPropertiesString())
             ->replace('constants', $this->getConstantsString())
             ->replace('table', $this->modelDefinition->getTable())
             ->replace('timestamps', $this->modelDefinition->hasTimestamps() ? 'true' : 'false')
             ->replace('uses', $this->getUses())
             ->replace('guarded', $this->getGuarded())
             ->replace('casts', $this->getCasts())
             ->replace('dates', $this->getDates())
             ->replace('valueConstants', $this->getValueConstants());

        return parent::write($file, $override);
    }

    /**
     * @param ModelDefinition $modelDefinition
     */
    public function setModelDefinition(ModelDefinition $modelDefinition): void
    {
        $this->modelDefinition = $modelDefinition;

        if ($this->modelDefinition->hasTimestamps()) {

            // @TODO: find a better solution to add the model information to the column
            $createdAt        = ColumnParser::parse('created_at', 'timestamp');
            $createdAt->model = $this->modelDefinition->getModel();
            $this->modelDefinition->addColumn($createdAt);

            // @TODO: find a better solution to add the model information to the column
            $updatedAt        = ColumnParser::parse('updated_at', 'timestamp');
            $updatedAt->model = $this->modelDefinition->getModel();
            $this->modelDefinition->addColumn($updatedAt);
        }

        if ($this->modelDefinition->usesCarbon()) {
            $this->uses->add('Carbon\Carbon');
        }
    }

    /**
     * @return string
     */
    private function getPropertiesString(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf(' * @property %s $%s', $column->phpDataType, $column->name);
        })->implode(PHP_EOL);
    }

    /**
     * @return string
     */
    private function getConstantsString(): string
    {
        return $this->modelDefinition->getColumns()->map(function (Column $column) {
            return sprintf('    const PROPERTY_%s = \'%s\';', Str::upper($column->name), $column->name);
        })->implode(PHP_EOL);
    }

    /**
     * @return string
     */
    private function getUses(): string
    {
        $string = $this->uses
            ->unique()
            ->filter(function (string $class) {
                return $this->getNamespace($class) !== 'App\Models';
            })
            ->map(function (string $class) {
                return sprintf('use %s;', $class);
            })->implode(PHP_EOL);

        if ($this->uses->unique()->count() > 0) {
            $string = PHP_EOL . $string . PHP_EOL;
        }

        return $string;
    }

    /**
     * @param string $class
     *
     * @return string
     */
    private function getNamespace(string $class): string
    {
        $parts = explode('\\', $class);
        array_pop($parts);

        return implode('\\', $parts);
    }

    /**
     * @return string
     */
    private function getGuarded(): string
    {
        return '        self::PROPERTY_ID,';
    }

    /**
     * @return string
     */
    private function getDates(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->phpDataType === 'Carbon';
            })->map(function (Column $column) {
                return sprintf('        self::PROPERTY_%s,', Str::upper($column->name));
            })->implode(PHP_EOL);
    }

    /**
     * @return string
     */
    private function getCasts(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return !in_array($column->phpDataType, ['string', 'Carbon']);
            })->map(function (Column $column) {
                return sprintf('        self::PROPERTY_%s => \'%s\',', Str::upper($column->name), $column->phpDataType);
            })->implode(PHP_EOL);
    }

    private function getValueConstants(): string
    {
        $valueConstants = [];

        foreach ($this->modelDefinition->getValues() as $column => $values) {
            foreach ($values as $value) {
                $valueConstants[] = sprintf(
                    '%sconst %s_%s = \'%s\';',
                    "\t",
                    Str::upper($column),
                    Str::upper($value),
                    $value
                );
            }
        }

        return implode(PHP_EOL, $valueConstants);
    }
}

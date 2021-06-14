<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ClassConstantsStrings;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ClassPropertyStrings;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ModelCasts;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ModelDates;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ModelRelations;
use SimKlee\LaravelBakery\Generator\Formatter\Migrations\ModelValueConstants;
use SimKlee\LaravelBakery\Model\ModelDefinition;
use SimKlee\LaravelBakery\Support\ClassHelper;

/**
 * Class ModelWriter
 * @package SimKlee\LaravelBakery\Generator
 */
class ModelWriter extends AbstractWriter
{
    protected string     $stubFile = 'model.stub';
    protected Collection $uses;

    /**
     * ModelWriter constructor.
     *
     * @param ModelDefinition $modelDefinition
     */
    public function __construct(ModelDefinition $modelDefinition)
    {
        $this->uses = new Collection();
        parent::__construct($modelDefinition);
    }

    protected function handleVars(): void
    {
        $extends = config('laravel-bakery.model.base_model');
        $this->uses->add($extends);

        $this->setVar('Model', $this->modelDefinition->getModel());
        $this->setVar('table', $this->modelDefinition->getTable());
        $this->setVar('extends', class_basename($extends));
        $this->setVar('timestamps', $this->modelDefinition->hasTimestamps() ? 'true' : 'false');
        $this->setVar('uses', $this->getClassUses());
        $this->setVar('guarded', $this->getGuarded());
        $this->setVar('properties', (new ClassPropertyStrings($this->modelDefinition))->toString());
        $this->setVar('constants', (new ClassConstantsStrings($this->modelDefinition))->toString());
        $this->setVar('casts', (new ModelCasts($this->modelDefinition))->toString());
        $this->setVar('dates', (new ModelDates($this->modelDefinition))->toString());
        $this->setVar('valueConstants', (new ModelValueConstants($this->modelDefinition))->toString());
        $this->setVar('relations', (new ModelRelations($this->modelDefinition))->toString());
    }

    private function getClassUses(): string
    {
        $string = $this->uses
            ->unique()
            ->filter(function (string $class) {
                return !ClassHelper::inNamespace($class, 'App\Models');
            })
            ->map(function (string $class) {
                return sprintf('use %s;', $class);
            })->implode(PHP_EOL);

        if ($this->uses->unique()->count() > 0) {
            $string = PHP_EOL . $string . PHP_EOL;
        }

        return $string;
    }

    private function getGuarded(): string
    {
        return '        self::PROPERTY_ID,';
    }
}

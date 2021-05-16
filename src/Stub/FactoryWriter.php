<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Models\ModelDefinition;
use Str;
use function PHPUnit\TestFixture\func;

/**
 * Class FactoryWriter
 * @package SimKlee\LaravelBakery\Support
 */
class FactoryWriter extends Stub
{
    /**
     * @var ModelDefinition
     */
    private ModelDefinition $modelDefinition;

    /**
     *
     * @param ModelDefinition $modelDefinition
     *
     * @return FactoryWriter
     */
    public static function fromModelDefinition(ModelDefinition $modelDefinition): FactoryWriter
    {
        $writer = new FactoryWriter('factory.stub',);
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
        $this->replace('Model', $this->modelDefinition->getModel())
             ->replace('definitions', $this->getDefinitions());

        return parent::write($file, $override);
    }

    private function getDefinitions(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->filter(function (Column $column) {
                return $column->primaryKey === false;
            })
            ->map(function (Column $column) {
                return sprintf(
                    '%s%s => null,',
                    "\t\t\t",
                    $column->getPropertyString()
                );
            })
            ->implode(PHP_EOL);
    }
}

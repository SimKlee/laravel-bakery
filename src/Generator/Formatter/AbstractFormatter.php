<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter;

use SimKlee\LaravelBakery\Model\ModelDefinition;

/**
 * Class AbstractFormatter
 * @package SimKlee\LaravelBakery\Stub\Formatter
 */
abstract class AbstractFormatter
{
    protected ModelDefinition $modelDefinition;

    /**
     * AbstractFormatter constructor.
     *
     * @param ModelDefinition $modelDefinition
     */
    public function __construct(ModelDefinition $modelDefinition)
    {
        $this->modelDefinition = $modelDefinition;
    }

    /**
     * @param string          $class
     * @param ModelDefinition $modelDefinition
     *
     * @return AbstractFormatter
     */
    public static function factory(string $class, ModelDefinition $modelDefinition): AbstractFormatter
    {
        return new $class($modelDefinition);
    }

    /**
     * @return string
     */
    abstract public function toString(): string;
}

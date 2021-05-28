<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use SimKlee\LaravelBakery\Model\ModelDefinition;

/**
 * Class AbstractWriter
 * @package SimKlee\LaravelBakery\Generator
 */
abstract class AbstractWriter implements GeneratorInterface
{
    protected string          $stubFile = '';
    protected Stub            $stub;
    protected ModelDefinition $modelDefinition;

    /**
     * ModelWriter constructor.
     *
     * @param ModelDefinition $modelDefinition
     */
    public function __construct(ModelDefinition $modelDefinition)
    {
        $this->stub            = new Stub($this->stubFile);
        $this->modelDefinition = $modelDefinition;
        $this->handleVars();
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setVar(string $key, $value): void
    {
        $this->stub->setVar($key, $value);
    }

    /**
     * @param string $file
     * @param bool   $override
     *
     * @return bool|int
     */
    public function write(string $file, bool $override = false)
    {
        return $this->stub->write($file, $override);
    }

    abstract protected function handleVars(): void;
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class Stub
 * @package SimKlee\LaravelBakery\Generator
 */
class Stub implements GeneratorInterface
{
    protected string $stub;
    protected string $path = __DIR__ . '/../../resources/stubs/';
    protected string $content;
    protected array  $vars;

    public function __construct(string $stub, string $path = null)
    {
        $this->stub = $stub;
        if (!is_null($path)) {
            $this->path = $path;
        }

        $this->loadStub();
    }

    protected function loadStub(): bool
    {
        try {
            $this->content = File::get($this->path . $this->stub);

            return true;
        } catch (FileNotFoundException $e) {
            return false;
        }
    }

    protected function replace(string $property, string $value): Stub
    {
        $this->content = str_replace(sprintf('{{ %s }}', $property), $value, $this->content);

        return $this;
    }

    public function getContent(): string
    {
        foreach ($this->vars as $key => $value) {
            $this->replace($key, $value);
        }

        return $this->content;
    }

    /**
     * @return bool|int
     */
    public function write(string $file, bool $override = false)
    {
        if (!File::exists($file) || $override) {
            return File::put($file, $this->getContent());
        }

        return false;
    }

    public function setVar(string $key, $value): Stub
    {
        $this->vars[ $key ] = $value;

        return $this;
    }
}

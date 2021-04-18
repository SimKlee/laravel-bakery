<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use File;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class Stub
 * @package SimKlee\LaravelBakery\Stub
 */
class Stub
{
    /**
     * @var string
     */
    private string $stub;

    /**
     * @var string
     */
    private string $path = __DIR__ . '/../../resources/stubs/';

    /**
     * @var string
     */
    private string $content;

    /**
     * Stub constructor.
     *
     * @param string      $stub
     * @param string|null $path
     */
    public function __construct(string $stub, string $path = null)
    {
        $this->stub = $stub;
        if (!is_null($path)) {
            $this->path = $path;
        }

        $this->loadStub();
    }

    /**
     * @return bool
     */
    private function loadStub(): bool
    {
        try {
            $this->content = File::get($this->path . $this->stub);

            return true;
        } catch (FileNotFoundException $e) {
            return false;
        }
    }

    /**
     * @param string $property
     * @param string $value
     *
     * @return Stub
     */
    public function replace(string $property, string $value): Stub
    {
        $this->content = str_replace(sprintf('[[%s]]', $property), $value, $this->content);

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $file
     *
     * @return bool|int
     */
    public function write(string $file)
    {
        return File::put($file, $this->getContent());
    }
}
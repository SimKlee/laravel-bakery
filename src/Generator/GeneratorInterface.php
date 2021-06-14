<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

/**
 * Interface GeneratorInterface
 * @package SimKlee\LaravelBakery\Generator
 */
interface GeneratorInterface
{
    public function setVar(string $key, $value): Stub;

    /**
     * @return bool|int
     */
    public function write(string $file, bool $override = false);
}

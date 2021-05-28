<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator;

/**
 * Interface GeneratorInterface
 * @package SimKlee\LaravelBakery\Generator
 */
interface GeneratorInterface
{
    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setVar(string $key, $value): void;

    /**
     * @param string $file
     * @param bool   $override
     *
     * @return bool|int
     */
    public function write(string $file, bool $override = false);
}

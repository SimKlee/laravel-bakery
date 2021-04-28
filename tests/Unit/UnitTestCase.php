<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Class TestCase
 * @package SimKlee\LaravelBakery\Tests
 */
class UnitTestCase extends TestCase
{
    /**
     * @param string|null $path
     *
     * @return string
     */
    public function getResourcePath(string $path = null): string
    {
        $resourcePath = __DIR__ . '/../resources/';
        if (!is_null($path)) {
            $resourcePath .= $path;
        }

        return $resourcePath;
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use SimKlee\LaravelBakery\Providers\LaravelBakeryServiceProvider;

/**
 * Class TestCase
 * @package SimKlee\LaravelBakery\Tests
 */
class TestCase extends BaseTestCase
{
    /**
     * @param Application $app
     *
     * @return string[]
     */
    protected function getPackageProviders(Application $app): array
    {
        return [
            LaravelBakeryServiceProvider::class,
        ];
    }
}
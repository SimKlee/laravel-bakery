<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use SimKlee\LaravelBakery\Providers\LaravelBakeryServiceProvider;

/**
 * Class TestCase
 * @package SimKlee\LaravelBakery\Tests
 */
class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelBakeryServiceProvider::class,
        ];
    }
}

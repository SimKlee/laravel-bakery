<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Support;

use Illuminate\Support\Str;

/**
 * Class ModelHelper
 * @package SimKlees\LaravelBakery\Support
 */
class ModelHelper
{
    /**
     * @param string $model
     *
     * @return string
     */
    public static function model2Table(string $model): string
    {
        return Str::plural(Str::snake($model));
    }
}

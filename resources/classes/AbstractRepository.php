<?php declare(strict_types=1);

namespace App\Models\Repositories;

/**
 * Class AbstractRepository
 * @package App\Models\Repositories
 */
abstract class AbstractRepository
{
    /**
     * @param string $modelClass
     *
     * @return AbstractRepository
     */
    public static function create(string $modelClass): AbstractRepository
    {
        $object = sprintf('\App\Models\Repositories\%sRepository', class_basename($modelClass));
        return new $object();
    }
}

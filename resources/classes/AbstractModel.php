<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AbstractModel
 * @package App\Models
 */
abstract class AbstractModel extends Model
{
    use HasFactory;

    const TABLE = null;

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return string
     */
    public static function column(string $column, string $alias = null): string
    {
        if (is_null($alias)) {
            return sprintf('%s.%s', static::TABLE, $column);
        }

        return sprintf('%s.%s AS %s', static::TABLE, $column, $alias);
    }

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return class_basename($this);
    }

    /**
     * @return AbstractRepository
     */
    public static function repository(): AbstractRepository
    {
        return AbstractRepository::create(static::class);
    }
}

<?php declare(strict_types=1);

namespace App\Models;

use App\Models\Repositories\AbstractRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AbstractModel
 * @package App\Models
 *
 * @method static AbstractModel find(mixed $id, array $columns = [])
 * @method static AbstractModel create(array $attributes = [])
 * @method static AbstractModel firstOrCreate(array $attributes = [], array $values = [])
 */
abstract class AbstractModel extends Model
{
    use HasFactory;

    const TABLE = null;

    public static function column(string $column, string $alias = null): string
    {
        if (is_null($alias)) {
            return sprintf('%s.%s', static::TABLE, $column);
        }

        return sprintf('%s.%s AS %s', static::TABLE, $column, $alias);
    }

    public function getModelName(): string
    {
        return class_basename($this);
    }

    public static function repository(): AbstractRepository
    {
        return AbstractRepository::create(static::class);
    }
}

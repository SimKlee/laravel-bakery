<?php declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\AbstractModel;
use Str;

/**
 * Trait UuidTrait
 * @package App\Models\Traits
 */
trait UuidTrait
{
    public static function bootUuidTrait()
    {
        self::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return self::PROPERTY_UUID;
    }

    protected function getArrayableItems(array $values): array
    {
        /** @var AbstractModel $this */
        if (!in_array('hidden', $this->hidden)) {
            $this->hidden[] = self::PROPERTY_ID;
        }

        return parent::getArrayableItems($values);
    }

    public static function findByUuid(string $uuid): AbstractModel
    {
        return static::where(Static::PROPERTY_UUID, $uuid)->first();
    }
}

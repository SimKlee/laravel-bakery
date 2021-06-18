<?php declare(strict_types=1);

namespace App\Models\Traits;

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
}

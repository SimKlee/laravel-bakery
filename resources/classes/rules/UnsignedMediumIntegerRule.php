<?php declare(strict_types=1);

namespace App\Rules;

/**
 * Class UnsignedMediumIntegerRule
 * @package App\Rules
 */
class UnsignedMediumIntegerRule extends AbstractIntegerRule
{
    protected int $min = 0;
    protected int $max = 16777215;
}

<?php declare(strict_types=1);

namespace App\Rules;

/**
 * Class UnsignedSmallIntegerRule
 * @package App\Rules
 */
class UnsignedSmallIntegerRule extends AbstractIntegerRule
{
    protected int $min = 0;
    protected int $max = 65535;
}

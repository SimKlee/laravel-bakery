<?php declare(strict_types=1);

namespace App\Rules;

/**
 * Class UnsignedIntegerRule
 * @package App\Rules
 */
class UnsignedIntegerRule extends AbstractIntegerRule
{
    protected int $min = 0;
    protected int $max = 4294967295;
}

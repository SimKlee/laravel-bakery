<?php declare(strict_types=1);

namespace App\Rules;

/**
 * Class UnsignedTinyIntegerRule
 * @package App\Rules
 */
class UnsignedTinyIntegerRule extends AbstractIntegerRule
{
    protected int $min = 0;
    protected int $max = 255;
}

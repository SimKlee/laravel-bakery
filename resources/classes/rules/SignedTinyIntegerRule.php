<?php declare(strict_types=1);

namespace App\Rules;

/**
 * Class SignedTinyIntegerRule
 * @package App\Rules
 */
class SignedTinyIntegerRule extends AbstractIntegerRule
{
    protected int $min = -128;
    protected int $max = 127;
}

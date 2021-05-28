<?php declare(strict_types=1);

namespace App\Rules;

/**
 * Class SignedIntegerRule
 * @package App\Rules
 */
class SignedIntegerRule extends AbstractIntegerRule
{
    protected int $min = -2147483648;
    protected int $max = 2147483647;
}

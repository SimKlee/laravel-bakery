<?php declare(strict_types=1);

namespace App\Rules;

/**
 * Class SignedSmallIntegerRule
 * @package App\Rules
 */
class SignedSmallIntegerRule extends AbstractIntegerRule
{
    protected int $min = -32768;
    protected int $max = 32767;
}

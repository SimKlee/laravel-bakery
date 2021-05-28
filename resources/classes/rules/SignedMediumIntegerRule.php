<?php declare(strict_types=1);

namespace App\Rules;

/**
 * Class SignedMediumIntegerRule
 * @package App\Rules
 */
class SignedMediumIntegerRule extends AbstractIntegerRule
{
    protected int $min = -8388608;
    protected int $max = 8388607;
}

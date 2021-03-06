<?php declare(strict_types=1);

namespace App\Rules;

/**
 * Class AbstractIntegerRule
 * @package App\Rules
 */
abstract class AbstractIntegerRule extends AbstractRule
{
    protected int $min = 0;
    protected int $max = 0;

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return $value >= $this->min && $value <= $this->max;
    }

    public function message(): string
    {
        return sprintf('The :attribute must be between %s and %s.', $this->min, $this->max);
    }
}

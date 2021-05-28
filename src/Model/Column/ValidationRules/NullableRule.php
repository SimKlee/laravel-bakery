<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\ValidationRules;

/**
 * Class NullableRule
 * @package SimKlee\LaravelBakery\Model\Column\ValidationRules
 */
class NullableRule extends AbstractRule
{
    public function handle(): void
    {
        if ($this->column->nullable) {
            $this->rules->add('nullable');
        } else {
            $this->rules->add('required');
        }
    }
}

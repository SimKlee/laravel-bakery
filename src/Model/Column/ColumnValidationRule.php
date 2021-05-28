<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use SimKlee\LaravelBakery\Model\Column\ValidationRules\FloatRule;
use SimKlee\LaravelBakery\Model\Column\ValidationRules\LengthRule;
use SimKlee\LaravelBakery\Model\Column\ValidationRules\NullableRule;
use SimKlee\LaravelBakery\Model\Column\ValidationRules\TypeRule;
use SimKlee\LaravelBakery\Support\Collection;

/**
 * Class ColumnValidationRule
 * @package SimKlees\LaravelBakery\Model\Column
 */
class ColumnValidationRule
{
    private Column $column;
    private array  $ruleClasses = [
        TypeRule::class,
        FloatRule::class,
        NullableRule::class,
        LengthRule::class,
    ];

    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function getRule(): string
    {
        $rules = new Collection();
        collect($this->ruleClasses)->each(function (string $class) use ($rules) {
            $rules->merge((new $class($this->column))->getRules());
        });

        return sprintf("\t\t\t%s => '%s'", $this->column->getPropertyString(), $rules->implode('|'));
    }
}

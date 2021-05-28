<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\ValidationRules;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Model\Column\Column;

/**
 * Class AbstractRule
 * @package SimKlee\LaravelBakery\Model\Column\ValidationRules
 *
 * @see https://laravel.com/docs/8.x/validation#available-validation-rules
 * @see https://www.esparkinfo.com/laravel-custom-validation-rules.html
 */
abstract class AbstractRule
{
    protected Column     $column;
    protected Collection $rules;

    public function __construct(Column $column)
    {
        $this->column = $column;
        $this->rules  = new Collection();
    }

    public function getRules(): Collection
    {
        return $this->rules;
    }

    abstract public function handle(): void;
}

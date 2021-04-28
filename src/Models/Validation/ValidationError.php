<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models\Validation;

class ValidationError
{
    public $column;
    public $type;
    public $expected;
    public $actual;
}

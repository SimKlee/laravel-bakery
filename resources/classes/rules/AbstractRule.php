<?php declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class AbstractRule
 * @package App\Rules
 */
abstract class AbstractRule implements Rule
{
    const RULE_REQUIRED = 'required';
    const RULE_NULLABLE = 'nullable';
}

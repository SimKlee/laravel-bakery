<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\ValidationRules;

use SimKlee\LaravelBakery\Model\Column\Exceptions\MissingValidationRuleForDataType;

/**
 * Class TypeRule
 * @package SimKlee\LaravelBakery\Model\Column\ValidationRules
 */
class TypeRule extends AbstractRule
{
    private array $dataTypeMap = [
        'tinyInteger'   => 'integer',
        'smallInteger'  => 'integer',
        'mediumInteger' => 'integer',
        'integer'       => 'integer',
        'bigInteger'    => 'integer',
        'varchar'       => 'string',
        'char'          => 'string',
        'text'          => 'string',
        'boolean'       => 'boolean',
        'dateTime'      => 'date',
        'date'          => 'date',
        'time'          => 'date',
        'timestamp'     => 'date',
    ];

    public function handle(): void
    {
        $this->rules->add($this->dataTypeMap[ $this->column->dataType ]);
    }
}

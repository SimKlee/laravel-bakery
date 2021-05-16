<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models;

/**
 * Class ColumnValidation
 * @package SimKlees\LaravelBakery\Models
 */
class ColumnValidation
{
    /**
     * @var Column
     */
    private $column;

    /**
     * @param Column $column
     */
    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function getRule(): string
    {
        $rules = collect();
        switch ($this->column->dataType) {
            case 'tinyInteger':
            case 'smallInteger':
            case 'mediumInteger':
            case 'integer':
            case 'bigInteger':
                $rules->add('integer');
                break;

            case 'varchar':
            case 'char':
            case 'text':
                $rules->add('string');
                break;

            case 'decimal':
            case 'float':
            case 'boolean':
                $rules->add('boolean');
                break;

            case 'dateTime':
            case 'date':
            case 'time':
            case 'timestamp':
                $rules->add('date');
                break;
        }

        if ($this->column->nullable) {
            $rules->add('nullable');
        } else {
            $rules->add('required');
        }

        if ($this->column->length) {
            $rules->add('max:' . $this->column->length);
        }

        return sprintf(
            '%s%s => \'%s\',',
            "\t\t\t",
            $this->column->getPropertyString(),
            $rules->implode('|')
        );
    }
}

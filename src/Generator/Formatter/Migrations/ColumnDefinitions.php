<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Migrations;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Generator\Stub;
use SimKlee\LaravelBakery\Model\Column\Exceptions\UnknownMethodForDataTypeException;
use SimKlee\LaravelBakery\Model\Column\Column;

/**
 * Class ColumnDefinitions
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ColumnDefinitions extends AbstractFormatter
{
    private array $methodMap = [
        'tinyInteger'   => 'tinyInteger',
        'integer'       => 'integer',
        'smallInteger'  => 'smallInteger',
        'mediumInteger' => 'mediumInteger',
        'bigInteger'    => 'bigInteger',
        'varchar'       => 'string',
        'char'          => 'char',
        'text'          => 'text',
        'timestamp'     => 'timestamp',
        'boolean'       => 'boolean',
        'decimal'       => 'decimal',
        'dateTime'      => 'dateTime',
    ];

    public function toString(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->map(function (Column $column) {
                return $this->getColumnMigration($column);
            })->implode(PHP_EOL);
    }

    /**
     * @throws UnknownMethodForDataTypeException
     */
    private function getColumnMigration(Column $column): string
    {
        $stub = new Stub('migrations/column_definition.stub');
        $stub->setVar('method', $this->getMethod($column))
             ->setVar('params', $this->getMethodParams($column))
             ->setVar('attributeMethods', $this->getAttributeMethods($column));

        return $stub->getContent();
    }

    /**
     * @throws UnknownMethodForDataTypeException
     */
    private function getMethod(Column $column): string
    {
        if (!isset($this->methodMap[ $column->dataType ])) {
            throw new UnknownMethodForDataTypeException(sprintf('Unknown method for data type "%s"', $column->dataType));
        }

        return ($column->unsigned)
            ? 'unsigned' . ucfirst($this->methodMap[ $column->dataType ])
            : $this->methodMap[ $column->dataType ];
    }

    private function getMethodParams(Column $column): string
    {
        $params = collect([$column->getPropertyString()]);

        if ($column->autoIncrement) {
            $params->add(true);
        }

        if ($column->length) {
            $params->add($column->length);
        }

        if ($column->precision) {
            $params->add($column->precision);
        }

        return $params->map(function ($param, $i) {
            if ($i === 0) {
                return $param;
            }

            return $this->cast($param);
        })->implode(', ');
    }

    private function getAttributeMethods(Column $column): string
    {
        $methods = new Collection();

        if ($column->nullable) {
            $methods->add('->nullable()');
        }

        if ($column->default) {
            $methods->add(sprintf('->default(%s)', $this->cast($column->default)));
        }

        return $methods->implode('');
    }

    /**
     * @param mixed $value
     *
     * @return string|float|int
     */
    private function cast($value)
    {
        if (is_string($value)) {
            return sprintf("'%s'", $value);
        } else {
            if (is_bool($value)) {
                return $value === true ? 'true' : 'false';
            }
        }

        return $value;
    }
}

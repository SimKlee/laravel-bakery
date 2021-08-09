<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Migrations;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Generator\Stub;
use SimKlee\LaravelBakery\Model\Column\ColumnDataType;
use SimKlee\LaravelBakery\Model\Column\Exceptions\UnknownMethodForDataTypeException;
use SimKlee\LaravelBakery\Model\Column\Column;

/**
 * Class ColumnDefinitions
 * @package SimKlee\LaravelBakery\Stub\Formatter\ColumnHelper
 */
class ColumnDefinitions extends AbstractFormatter
{
    private array $methodMap = [
        ColumnDataType::DATA_TYPE_TINY_INTEGER   => 'tinyInteger',
        ColumnDataType::DATA_TYPE_INTEGER        => 'integer',
        ColumnDataType::DATA_TYPE_SMALL_INTEGER  => 'smallInteger',
        ColumnDataType::DATA_TYPE_MEDIUM_INTEGER => 'mediumInteger',
        ColumnDataType::DATA_TYPE_BIG_INTEGER    => 'bigInteger',
        ColumnDataType::DATA_TYPE_VARCHAR        => 'string',
        ColumnDataType::DATA_TYPE_CHAR           => 'char',
        ColumnDataType::DATA_TYPE_TEXT           => 'text',
        ColumnDataType::DATA_TYPE_MEDIUM_TEXT    => 'mediumText',
        ColumnDataType::DATA_TYPE_LONG_TEXT      => 'longText',
        ColumnDataType::DATA_TYPE_JSON           => 'json',
        ColumnDataType::DATA_TYPE_TIMESTAMP      => 'timestamp',
        ColumnDataType::DATA_TYPE_BOOLEAN        => 'boolean',
        ColumnDataType::DATA_TYPE_FLOAT          => 'float',
        ColumnDataType::DATA_TYPE_DOUBLE         => 'double',
        ColumnDataType::DATA_TYPE_DECIMAL        => 'decimal',
        ColumnDataType::DATA_TYPE_DATETIME       => 'dateTime',
        ColumnDataType::DATA_TYPE_DATE           => 'date',
        ColumnDataType::DATA_TYPE_UUID           => 'uuid',
        ColumnDataType::DATA_TYPE_BINARY         => 'binary',
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

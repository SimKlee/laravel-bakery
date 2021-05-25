<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub\Formatter\Migrations;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Database\Migration\ColumnHelper;
use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Stub\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Stub\Stub;

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
        'dateTime'       => 'dateTime',
    ];

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->modelDefinition
            ->getColumns()
            ->map(function (Column $column) {
                return $this->getColumnMigration($column);
            })->implode(PHP_EOL);
    }

    /**
     * @param Column $column
     *
     * @return string
     * @throws \Exception
     */
    private function getColumnMigration(Column $column): string
    {
        $stub = new Stub('column_definition.stub');
        $stub->replace('method', $this->getMethod($column))
            ->replace('params', $this->getMethodParams($column))
            ->replace('attributeMethods', $this->getAttributeMethods($column));

        #return $stub->getContent();

        $string = "\t\t\t";
        $string .= '$table->';
        $string .= $this->getMethod($column);
        $string .= sprintf('(%s)', $this->getMethodParams($column));
        $string .= $this->getAttributeMethods($column);
        $string .= ';';

        return $string;
    }

    /**
     * @param Column $column
     *
     * @return string
     * @throws \Exception
     */
    private function getMethod(Column $column): string
    {
        $map = [
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
            'dateTime'       => 'dateTime',
        ];

        if (!isset($map[ $column->dataType ])) {
            throw new \Exception('Unknown method for data type ' . $column->dataType);
        }

        return ($column->unsigned)
            ? 'unsigned' . ucfirst($map[ $column->dataType ])
            : $map[ $column->dataType ];
    }

    /**
     * @param Column $column
     *
     * @return string
     */
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

    /**
     * @param Column $column
     *
     * @return string
     */
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

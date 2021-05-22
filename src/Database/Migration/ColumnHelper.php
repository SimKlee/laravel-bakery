<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Database\Migration;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use SimKlee\LaravelBakery\Models\Column;

/**
 * Class ColumnHelper
 * @package SimKlee\LaravelBakery\Database\Migration
 */
class ColumnHelper
{
    /**
     * @param Column $column
     *
     * @return string
     * @throws \Exception
     */
    public function getColumnMigration(Column $column): string
    {
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
     * @return string|false
     * @throws \Exception
     */
    public function getColumnIndexes(Column $column)
    {
        $type = null;

        if ($column->index) {
            $type = 'index';
        } else if ($column->unique) {
            $type = 'unique';
        }

        if ($type) {
            return sprintf("\t\t\t\$table->%s([%s], %s);", $type, $column->getPropertyString(), $column->getPropertyString());
        }

        return false;
    }

    public function getColumnForeignKey(Column $column)
    {
        if ($column->foreignKey === false) {
            return false;
        }

        // @TODO: foreign key name: fk__product_has_insurances__product_id (fk__table__column)
        $foreignKeyName = sprintf('fk__%s__%s', Str::plural(Str::snake($column->model)), $column->name);

        $foreignKey = sprintf("\t\t\t\$table->foreign(%s, '%s')\n", $column->getPropertyString(), $foreignKeyName);

        // @TODO: simplify FQN and add model to use
        $foreignKey .= sprintf("\t\t\t\t->on(\App\Models\%s::TABLE)\n", $column->foreignKeyColumn->model);
        $foreignKey .= sprintf("\t\t\t\t->references(\App\Models\%s);", $column->foreignKeyColumn->getPropertyString());

        return $foreignKey;
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

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Database\Migration;

use Illuminate\Support\Collection;
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
        $string = '$this->';
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
            'integer' => 'integer',
            'varchar' => 'string',
            'char'    => 'char',
            'text'    => 'text',
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
        $params = collect([$column->name]);

        if ($column->autoIncrement) {
            $params->add(true);
        }

        if ($column->length) {
            $params->add($column->length);
        }

        if ($column->precision) {
            $params->add($column->precision);
        }

        return $params->map(function ($param) {
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

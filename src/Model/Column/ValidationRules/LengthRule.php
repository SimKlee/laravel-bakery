<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\ValidationRules;

use SimKlee\LaravelBakery\Model\Column\ColumnDataType;

/**
 * Class LengthRule
 * @package SimKlee\LaravelBakery\Model\Column\ValidationRules
 */
class LengthRule extends AbstractRule
{
    const SIGNED   = 'signed';
    const UNSIGNED = 'unsigned';

    private array $ranges = [
        ColumnDataType::DATA_TYPE_TINY_INTEGER   => [
            self::SIGNED   => [-128, 127],
            self::UNSIGNED => [0, 255],
        ],
        ColumnDataType::DATA_TYPE_SMALL_INTEGER  => [
            self::SIGNED   => [-32768, 32767],
            self::UNSIGNED => [0, 65535],
        ],
        ColumnDataType::DATA_TYPE_MEDIUM_INTEGER => [
            self::SIGNED   => [-8388608, 8388607],
            self::UNSIGNED => [0, 16777215],
        ],
        ColumnDataType::DATA_TYPE_INTEGER        => [
            self::SIGNED   => [-2147483648, 2147483647],
            self::UNSIGNED => [0, 4294967295],
        ],
        ColumnDataType::DATA_TYPE_BIG_INTEGER    => [
            self::SIGNED   => [PHP_INT_MAX * -1, PHP_INT_MAX - 1],
            self::UNSIGNED => [0, PHP_INT_MAX],
        ],
    ];

    public function handle(): void
    {
        if ($this->column->length) {
            switch ($this->column->phpDataType) {

                case ColumnDataType::PHP_DATA_TYPE_STRING:
                    $this->rules->add('max:' . $this->column->length);
                    break;

                case ColumnDataType::PHP_DATA_TYPE_INTEGER:
                    $key = $this->column->unsigned ? self::UNSIGNED : self::SIGNED;
                    [$min, $max] = $this->ranges[ $this->column->dataType ][ $key ];
                    $this->rules->add('min:' . $min);
                    $this->rules->add('max:' . $max);
                    break;

            }
        }
    }
}

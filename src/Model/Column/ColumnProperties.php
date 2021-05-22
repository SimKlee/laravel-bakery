<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use SimKlee\LaravelBakery\Models\Exceptions\NotACastableTypeException;

/**
 * Class ColumnProperties
 * @package SimKlee\LaravelBakery\Model\Column
 */
class ColumnProperties extends AbstractColumnComponent
{
    const PROPERTY_LENGTH    = 'length';
    const PROPERTY_DEFAULT   = 'default';
    const PROPERTY_PRECISION = 'precision';

    /**
     * @throws NotACastableTypeException
     */
    protected function parseDefinitions(): void
    {
        $this->column->definitions->each(function (string $item) {
            if (strpos($item, ':') !== false) {
                [$key, $value] = explode(':', $item);
                switch ($key) {
                    case self::PROPERTY_DEFAULT:
                        $this->column->default = $this->castValue($this->column->phpDataType, $value);
                        break;

                    case self::PROPERTY_LENGTH:
                        $this->column->length = (int) $value;
                        break;

                    case self::PROPERTY_PRECISION:
                        $this->column->precision = (int) $value;
                        break;
                }
            }
        });
    }

    /**
     * @return bool
     */
    protected function validate(): bool
    {
        $result = true;



        return $result;
    }

    /**
     * @param string $type
     * @param mixed  $value
     *
     * @return string|int|float|bool
     * @throws NotACastableTypeException
     */
    private function castValue(string $type, $value)
    {
        if ($this->column->dataType === ColumnDataType::DATA_TYPE_DECIMAL) {
            return round($value, $this->column->precision);
        }

        switch ($type) {
            case 'string':
                $value = (string) $value;
                break;

            case 'int':
                $value = (int) $value;
                break;

            case 'float':
                $value = (float) $value;
                break;

            case 'boolean':
                $value = $this->castBoolean($value);
                break;

            default:
                throw new NotACastableTypeException(sprintf('Unknown type "%s" for casting.', $type));
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    private function castBoolean($value): bool
    {
        if (strtolower($value) === 'true') {
            return true;
        }

        if (strtolower($value) === 'false') {
            return false;
        }

        return (bool) $value;
    }
}

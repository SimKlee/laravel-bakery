<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models;

use SimKlee\LaravelBakery\Support\Collection;
use Illuminate\Support\Str;
use SimKlee\LaravelBakery\Models\Exceptions\NotACastableTypeException;
use SimKlee\LaravelBakery\Models\Exceptions\WrongAttributeException;

/**
 * Class ColumnParser
 * @package SimKlees\LaravelBakery\Models
 */
class ColumnParser
{
    const ATTRIBUTE_AUTO_INCREMENT = 'ai';
    const ATTRIBUTE_NULLABLE       = 'nullable';
    const ATTRIBUTE_UNSIGNED       = 'unsigned';

    const PROPERTY_LENGTH  = 'length';
    const PROPERTY_DEFAULT = 'default';

    const INDEX_PRIMARY_KEY = 'pk';
    const INDEX_FOREIGN_KEY = 'fk';
    const INDEX_INDEX       = 'index';
    const INDEX_UNIQUE      = 'unique';

    /**
     * @var Column
     */
    private $column;

    /**
     * @var Collection
     */
    private $definitions;

    /**
     * @var array|string[]
     */
    private $dataTypes = [
        'integer'   => 'int',
        'varchar'   => 'string',
        'char'      => 'string',
        'text'      => 'string',
        'timestamp' => 'Carbon',
    ];

    /**
     * ColumnParser constructor.
     *
     * @param string $name
     * @param string $definition
     *
     * @throws WrongAttributeException
     */
    public function __construct(string $name, string $definition)
    {
        $this->column      = new Column($name);
        $this->definitions = Collection::explode($definition, '|');

        $this->parseDataType();
        $this->parseAttributes();
        $this->parseProperties();
        $this->parseIndexes();
    }

    /**
     * @return Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }

    /**
     * @param string $name
     * @param string $definition
     *
     * @return Column
     */
    public static function parse(string $name, string $definition): Column
    {
        return (new ColumnParser($name, $definition))
            ->getColumn();
    }

    private function parseAttributes(): void
    {
        $this->definitions->each(function (string $item) {
            switch ($item) {
                case self::ATTRIBUTE_AUTO_INCREMENT:
                    if ($this->column->phpDataType !== 'int') {
                        throw new WrongAttributeException('Wrong attribute "autoIncrement" for data type ' . $this->column->dataType);
                    }
                    $this->column->autoIncrement = true;
                    $this->column->primaryKey    = true;
                    break;

                case self::ATTRIBUTE_UNSIGNED:
                    if ($this->column->phpDataType !== 'int') {
                        throw new WrongAttributeException('Wrong attribute "unsigned" for data type ' . $this->column->dataType);
                    }
                    $this->column->unsigned = true;
                    break;

                case self::ATTRIBUTE_NULLABLE:
                    if ($this->column->autoIncrement) {
                        throw new WrongAttributeException('Column cannot be nullable for auto increment!');
                    }
                    $this->column->nullable = true;
                    break;
            }
        });
    }

    private function parseDataType(): void
    {
        $dataTypes = array_keys($this->dataTypes);
        $this->definitions->each(function (string $item) use ($dataTypes) {
            $item = strtolower($item);
            if (in_array($item, $dataTypes)) {
                $this->column->dataType    = $item;
                $this->column->phpDataType = $this->dataTypes[ $item ];

                return false;
            }

            return true;
        });
    }

    private function parseProperties(): void
    {
        $this->definitions->each(function (string $item) {
            if (strpos($item, ':') !== false) {
                [$key, $value] = explode(':', $item);
                switch ($key) {
                    case self::PROPERTY_DEFAULT:
                        $this->column->default = $this->castValue($this->column->phpDataType, $value);
                        break;

                    case self::PROPERTY_LENGTH:
                        $this->column->length = (int) $value;
                        break;
                }

            }
        });
    }

    private function parseIndexes(): void
    {
        $this->definitions->each(function (string $item) {
            switch ($item) {
                case self::INDEX_PRIMARY_KEY:
                    $this->column->primaryKey = true;
                    break;

                case self::INDEX_FOREIGN_KEY:
                    $this->column->foreignKey = true;
                    break;

                case self::INDEX_INDEX:
                    $this->column->index = true;
                    break;

                case self::INDEX_UNIQUE:
                    $this->column->unique = true;
                    break;
            }
        });
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
        switch ($type) {
            case 'int':
                $value = (int) $value;
                break;

            case 'string':
                $value = (string) $value;
                break;

            default:
                throw new NotACastableTypeException(sprintf('Unknown type "%s" for casting.', $type));
        }

        return $value;
    }

    /**
     * @param string $columnName
     *
     * @return string
     * @throws \Exception
     */
    public function getModelNameFromForeignKey(string $columnName): string
    {
        $parts = Collection::explode($columnName, '_');

        if ($parts->last() !== 'id') {
            throw new \Exception('A foreign key ends with _id: ' . $columnName);
        }

        $parts->pop();

        return $parts->camel(true);
    }
}

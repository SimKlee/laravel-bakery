<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models;

use SimKlee\LaravelBakery\Models\Exceptions\UnknwonDataTypeException;
use SimKlee\LaravelBakery\Support\Collection;
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

    const FOREIGN_KEY_ON_UPDATE = 'onupdate';
    const FOREIGN_KEY_ON_DELETE = 'ondelete';
    const FOREIGN_KEY_CASCADE   = 'cascade';
    const FOREIGN_KEY_RESTRICT  = 'restrict';

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
        'tinyInteger'   => 'int',
        'smallInteger'  => 'int',
        'mediumInteger' => 'int',
        'integer'       => 'int',
        'bigInteger'    => 'int',
        'varchar'       => 'string',
        'char'          => 'string',
        'text'          => 'string',
        'decimal'       => 'float',
        'float'         => 'float',
        'boolean'       => 'boolean',
        'dateTime'      => 'Carbon',
        'date'          => 'Carbon',
        'time'          => 'Carbon',
        'timestamp'     => 'Carbon',
    ];

    /**
     * @var array|string[]
     */
    private $dataTypeAliases = [
        'tinyint'       => 'tinyInteger',
        'tinyInt'       => 'tinyInteger',
        'tinyinteger'   => 'tinyInteger',
        'smallint'      => 'smallInteger',
        'smallInt'      => 'smallInteger',
        'smallinteger'  => 'smallInteger',
        'mediumint'     => 'mediumInteger',
        'mediumInt'     => 'mediumInteger',
        'mediuminteger' => 'mediumInteger',
        'int'           => 'integer',
        'biginteger'    => 'bigInteger',
        'bigint'        => 'bigInteger',
        'bigInt'        => 'bigInteger',
        'string'        => 'varchar',
        'bool'          => 'boolean',
        'datetime'      => 'dateTime',
    ];

    private $foreignKeyLookup = true;

    /**
     * ColumnParser constructor.
     *
     * @param string $name
     * @param string $definition
     * @param bool   $foreignKeyLookup
     *
     * @throws UnknwonDataTypeException
     * @throws WrongAttributeException
     */
    public function __construct(string $name, string $definition, bool $foreignKeyLookup = true)
    {
        $this->column           = new Column($name);
        $this->definitions      = Collection::explode($definition, '|');
        $this->foreignKeyLookup = $foreignKeyLookup;

        $this->parseDataType();
        $this->parseAttributes();
        $this->parseProperties();
        $this->parseIndexes();
        $this->parseForeignKeyAttributes();
    }

    /**
     * @return Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }

    /**
     * @param string $dataType
     *
     * @return string
     */
    private function normalizeDataType(string $dataType): string
    {
        if (isset($this->dataTypeAliases[ $dataType ])) {
            return $this->dataTypeAliases[ $dataType ];
        }

        return $dataType;
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
        $dataTypeDefinition = $this->normalizeDataType($this->definitions->get(0));
        if ($dataTypeDefinition === self::INDEX_FOREIGN_KEY) {
            $this->setForeignKeyDefinition();

            return;
        }

        if (!isset($this->dataTypes[ $dataTypeDefinition ])) {
            throw new UnknwonDataTypeException(sprintf('Unknown data type "%s"', $this->definitions->get(0)));
        }
        $this->column->dataType    = $dataTypeDefinition;
        $this->column->phpDataType = $this->dataTypes[ $this->column->dataType ];
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
     * @throws \Exception
     */
    private function parseForeignKeyAttributes(): void
    {
        $this->definitions->each(function (string $item) {
            if (strpos($item, ':')) {
                [$attribute, $value] = explode(':', $item);

                if (!in_array($attribute, [self::FOREIGN_KEY_ON_UPDATE, self::FOREIGN_KEY_ON_DELETE])) {
                    return true;
                }

                if (!in_array($value, [self::FOREIGN_KEY_CASCADE, self::FOREIGN_KEY_RESTRICT])) {
                    throw new \Exception(sprintf('Unknown value "%s"', $value));
                }

                switch ($attribute) {
                    case self::FOREIGN_KEY_ON_UPDATE:
                        $this->column->foreignKeyOnUpdate = $value;
                        break;

                    case self::FOREIGN_KEY_ON_DELETE:
                        $this->column->foreignKeyOnDelete = $value;
                        break;
                }
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
            throw new \Exception('A foreign key must end with _id: ' . $columnName);
        }

        $parts->pop();

        return $parts->camel(true);
    }

    public function setForeignKeyDefinition(): void
    {
        if ($this->foreignKeyLookup) {
            $modelDefinition           = ModelDefinition::fromConfig($this->getModelNameFromForeignKey($this->column->name));
            $pkColumn                  = $modelDefinition->getColumn('id');
            $this->column->dataType    = $pkColumn->dataType;
            $this->column->phpDataType = $pkColumn->phpDataType;
            $this->column->unsigned    = $pkColumn->unsigned;
        }
    }
}

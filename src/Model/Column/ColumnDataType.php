<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use SimKlee\LaravelBakery\Model\Exceptions\UnknownDataTypeException;
use SimKlee\LaravelBakery\Support\Collection;

/**
 * Class ColumnDataType
 * @package SimKlee\LaravelBakery\Model\Column
 */
class ColumnDataType extends AbstractColumnComponent
{
    const DATA_TYPE_TINY_INTEGER   = 'tinyInteger';
    const DATA_TYPE_SMALL_INTEGER  = 'smallInteger';
    const DATA_TYPE_MEDIUM_INTEGER = 'mediumInteger';
    const DATA_TYPE_INTEGER        = 'integer';
    const DATA_TYPE_BIG_INTEGER    = 'bigInteger';
    const DATA_TYPE_VARCHAR        = 'varchar';
    const DATA_TYPE_CHAR           = 'char';
    const DATA_TYPE_TEXT           = 'text';
    const DATA_TYPE_MEDIUM_TEXT    = 'mediumText';
    const DATA_TYPE_LONG_TEXT      = 'longText';
    const DATA_TYPE_DECIMAL        = 'decimal';
    const DATA_TYPE_FLOAT          = 'float';
    const DATA_TYPE_DOUBLE         = 'double';
    const DATA_TYPE_BOOLEAN        = 'boolean';
    const DATA_TYPE_DATETIME       = 'dateTime';
    const DATA_TYPE_DATE           = 'date';
    const DATA_TYPE_TIME           = 'time';
    const DATA_TYPE_TIMESTAMP      = 'timestamp';
    const DATA_TYPE_UUID           = 'uuid';
    const DATA_TYPE_BINARY         = 'binary';
    const DATA_TYPE_JSON           = 'json';

    const PHP_DATA_TYPE_INTEGER = 'int';
    const PHP_DATA_TYPE_STRING  = 'string';
    const PHP_DATA_TYPE_FLOAT   = 'float';
    const PHP_DATA_TYPE_BOOLEAN = 'boolean';
    const PHP_DATA_TYPE_CARBON  = 'Carbon';
    const PHP_DATA_TYPE_ARRAY   = 'array';

    private array $phpDataTypes = [
        self::DATA_TYPE_TINY_INTEGER   => self::PHP_DATA_TYPE_INTEGER,
        self::DATA_TYPE_SMALL_INTEGER  => self::PHP_DATA_TYPE_INTEGER,
        self::DATA_TYPE_MEDIUM_INTEGER => self::PHP_DATA_TYPE_INTEGER,
        self::DATA_TYPE_INTEGER        => self::PHP_DATA_TYPE_INTEGER,
        self::DATA_TYPE_BIG_INTEGER    => self::PHP_DATA_TYPE_INTEGER,
        self::DATA_TYPE_VARCHAR        => self::PHP_DATA_TYPE_STRING,
        self::DATA_TYPE_CHAR           => self::PHP_DATA_TYPE_STRING,
        self::DATA_TYPE_TEXT           => self::PHP_DATA_TYPE_STRING,
        self::DATA_TYPE_MEDIUM_TEXT    => self::PHP_DATA_TYPE_STRING,
        self::DATA_TYPE_LONG_TEXT      => self::PHP_DATA_TYPE_STRING,
        self::DATA_TYPE_DECIMAL        => self::PHP_DATA_TYPE_FLOAT,
        self::DATA_TYPE_FLOAT          => self::PHP_DATA_TYPE_FLOAT,
        self::DATA_TYPE_DOUBLE         => self::PHP_DATA_TYPE_FLOAT,
        self::DATA_TYPE_BOOLEAN        => self::PHP_DATA_TYPE_BOOLEAN,
        self::DATA_TYPE_DATETIME       => self::PHP_DATA_TYPE_CARBON,
        self::DATA_TYPE_DATE           => self::PHP_DATA_TYPE_CARBON,
        self::DATA_TYPE_TIME           => self::PHP_DATA_TYPE_CARBON,
        self::DATA_TYPE_TIMESTAMP      => self::PHP_DATA_TYPE_CARBON,
        self::DATA_TYPE_UUID           => self::PHP_DATA_TYPE_STRING,
        self::DATA_TYPE_BINARY         => self::PHP_DATA_TYPE_STRING,
        self::DATA_TYPE_JSON           => self::PHP_DATA_TYPE_ARRAY,
    ];

    private array $dataTypeAliases = [
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

    /**
     * @throws UnknownDataTypeException
     */
    protected function parseDefinitions(): void
    {
        $dataType = $this->column->definitions->get(0);

        if ($dataType === 'fk') {
            return;
        }

        if (isset($this->dataTypeAliases[ $dataType ])) {
            $dataType = $this->dataTypeAliases[ $dataType ];
        }

        if (!isset($this->phpDataTypes[ $dataType ])) {
            throw new UnknownDataTypeException(sprintf('No php data type defined for "%s"', $dataType));
        }

        $this->column->dataType    = $dataType;
        $this->column->phpDataType = $this->phpDataTypes[ $dataType ];
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use SimKlee\LaravelBakery\Models\ColumnParser;
use SimKlee\LaravelBakery\Models\Exceptions\WrongAttributeException;

/**
 * Class ColumnParserTest
 * @package SimKlees\LaravelBakery\Tests\Unit\Models
 */
class ColumnParserTest extends TestCase
{
    /**
     * @return array
     */
    public function dataProviderForTestIntegerDefinitions(): array
    {
        return [
            [
                'name'               => 'IntegerColumn',
                'definition'         => 'integer',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'integer',
                    'phpDataType' => 'int',
                ]),
            ],
            [
                'name'               => 'IntegerColumn',
                'definition'         => 'integer|unsigned',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'integer',
                    'phpDataType' => 'int',
                    'unsigned'    => true,
                ]),
            ],
            [
                'name'               => 'IntegerColumn',
                'definition'         => 'integer|unsigned|ai|pk',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'      => 'integer',
                    'phpDataType'   => 'int',
                    'primaryKey'    => true,
                    'autoIncrement' => true,
                    'unsigned'      => true,
                ]),
            ],
            [
                'name'               => 'IntegerColumn',
                'definition'         => 'integer|unsigned|ai',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'      => 'integer',
                    'phpDataType'   => 'int',
                    'primaryKey'    => true,
                    'autoIncrement' => true,
                    'unsigned'      => true,
                ]),
            ],
            [
                'name'               => 'IntegerColumn',
                'definition'         => 'integer|default:10',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'integer',
                    'phpDataType' => 'int',
                    'default'     => 10,
                ]),
            ],
            [
                'name'               => 'IntegerColumn',
                'definition'         => 'integer|nullable',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'integer',
                    'phpDataType' => 'int',
                    'nullable'    => true,
                ]),
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataProviderForTestStringDefinitions(): array
    {
        return [
            [
                'name'               => 'VarcharColumn',
                'definition'         => 'varchar|length:250',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'varchar',
                    'phpDataType' => 'string',
                    'length'      => 250,
                ]),
            ],
            [
                'name'               => 'VarcharColumn',
                'definition'         => 'varchar|length:250',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'varchar',
                    'phpDataType' => 'string',
                    'length'      => 250,
                ]),
            ],
            [
                'name'               => 'VarcharColumn',
                'definition'         => 'varchar|length:50|default:teststring',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'varchar',
                    'phpDataType' => 'string',
                    'length'      => 50,
                    'default'     => 'teststring',
                ]),
            ],
            [
                'name'               => 'CharColumn',
                'definition'         => 'char|length:2',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'char',
                    'phpDataType' => 'string',
                    'length'      => 2,
                ]),
            ],
            [
                'name'               => 'CharColumn',
                'definition'         => 'char|length:2|default:DE',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'char',
                    'phpDataType' => 'string',
                    'length'      => 2,
                    'default'     => 'DE',
                ]),
            ],
            [
                'name'               => 'TextColumn',
                'definition'         => 'text',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'text',
                    'phpDataType' => 'string',
                ]),
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataProviderForTestIndexDefinitions(): array
    {
        return [
            [
                'name'               => 'IntegerColumn',
                'definition'         => 'integer|index',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'integer',
                    'phpDataType' => 'int',
                    'index'       => true,
                ]),
            ],
            [
                'name'               => 'IntegerColumn',
                'definition'         => 'integer|unique',
                'expectedProperties' => $this->getExpectedValues([
                    'dataType'    => 'integer',
                    'phpDataType' => 'int',
                    'unique'      => true,
                ]),
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTestIntegerDefinitions
     * @dataProvider dataProviderForTestStringDefinitions
     * @dataProvider dataProviderForTestIndexDefinitions
     *
     * @param string $name
     * @param string $definitions
     * @param array  $expectedProperties
     */
    public function testDefinitions(string $name, string $definitions, array $expectedProperties): void
    {
        $column = ColumnParser::parse($name, $definitions);
        $this->assertSame($name, $column->name, 'name of column');
        foreach ($expectedProperties as $property => $expectedValue) {
            $this->assertSame($expectedValue, $column->$property, 'property ' . $property);
        }
    }

    /**
     * @return \string[][]
     */
    public function dataProviderForTestWrongAttributes(): array
    {
        return [
            [
                'name'       => 'CharColumn',
                'definition' => 'char|unsigned',
            ],
            [
                'name'       => 'CharColumn',
                'definition' => 'char|ai',
            ],
            [
                'name'       => 'CharColumn',
                'definition' => 'char|ai|nullable',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTestWrongAttributes
     *
     * @param string $name
     * @param string $definition
     */
    public function testWrongAttributes(string $name, string $definition)
    {
        $this->expectException(WrongAttributeException::class);
        ColumnParser::parse($name, $definition);
    }

    /**
     * @param array $expectedValues
     *
     * @return array
     */
    private function getExpectedValues(array $expectedValues): array
    {
        return array_merge([
            'unsigned'      => false,
            'nullable'      => false,
            'autoIncrement' => false,
            'default'       => null,
            'length'        => null,
            'precision'     => null,
            'primaryKey'    => false,
            'foreignKey'    => false,
            'index'         => false,
            'unique'        => false,
        ], $expectedValues);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetModelNameFromForeignKeyDefinition(): array
    {
        return [
            [
                'columnName'        => 'foreign_key_id',
                'expectedModelName' => 'ForeignKey',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetModelNameFromForeignKeyDefinition
     *
     * @param string $columnName
     * @param string $expectedModelName
     */
    public function testGetModelNameFromForeignKeyName(string $columnName, string $expectedModelName): void
    {
        $parser = new ColumnParser($columnName, 'integer');
        $this->assertSame($expectedModelName, $parser->getModelNameFromForeignKey($columnName));
    }

}

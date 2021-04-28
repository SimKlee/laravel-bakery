<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Tests\Unit\Database\Migration;

use PHPUnit\Framework\TestCase;
use SimKlee\LaravelBakery\Database\Migration\ColumnHelper;
use SimKlee\LaravelBakery\Models\ColumnParser;

/**
 * Class ColumnHelperTest
 * @package SimKlees\LaravelBakery\Tests\Unit\Database\Migration
 */
class ColumnHelperTest extends TestCase
{
    public function dataProviderIntegerColumns(): array
    {
        return [
            [
                'column'            => 'IntegerColumn',
                'definition'        => 'integer',
                'expectedMigration' => '$this->integer(\'IntegerColumn\');',
            ],
            [
                'column'            => 'IntegerColumn',
                'definition'        => 'integer|ai',
                'expectedMigration' => '$this->integer(\'IntegerColumn\', true);',
            ],
            [
                'column'            => 'IntegerColumn',
                'definition'        => 'integer|unsigned',
                'expectedMigration' => '$this->unsignedInteger(\'IntegerColumn\');',
            ],
        ];
    }

    public function dataProviderStringColumns(): array
    {
        return [
            [
                'column'            => 'StringColumn',
                'definition'        => 'varchar|length:50',
                'expectedMigration' => '$this->string(\'StringColumn\', 50);',
            ],
            [
                'column'            => 'StringColumn',
                'definition'        => 'varchar|length:50|nullable',
                'expectedMigration' => '$this->string(\'StringColumn\', 50)->nullable();',
            ],
            [
                'column'            => 'StringColumn',
                'definition'        => 'varchar|length:50|default:test',
                'expectedMigration' => '$this->string(\'StringColumn\', 50)->default(\'test\');',
            ],
            [
                'column'            => 'CharColumn',
                'definition'        => 'char|length:2',
                'expectedMigration' => '$this->char(\'CharColumn\', 2);',
            ],
            [
                'column'            => 'CharColumn',
                'definition'        => 'char|length:2|default:DE',
                'expectedMigration' => '$this->char(\'CharColumn\', 2)->default(\'DE\');',
            ],
            [
                'column'            => 'TextColumn',
                'definition'        => 'text',
                'expectedMigration' => '$this->text(\'TextColumn\');',
            ],
        ];
    }

    /**
     * @dataProvider dataProviderIntegerColumns
     * @dataProvider dataProviderStringColumns
     *
     * @param string $column
     * @param string $definition
     * @param string $expectedMigration
     */
    public function testGetColumnMigration(string $column, string $definition, string $expectedMigration): void
    {
        $helper = new ColumnHelper();
        $column = ColumnParser::parse($column, $definition);

        $this->assertSame($expectedMigration, $helper->getColumnMigration($column));
    }

    public function testGetColumnMigrationThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown method for data type ');

        $helper = new ColumnHelper();
        $column = ColumnParser::parse('UnknownDataType', 'unknown');
        $helper->getColumnMigration($column);
    }
}

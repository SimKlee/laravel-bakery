<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use SimKlee\LaravelBakery\Models\ModelDefinitionsBag;

/**
 * Class ModelDefinitionBagTest
 * @package SimKlees\LaravelBakery\Tests\Unit\Models
 */
class ModelDefinitionBagTest extends TestCase
{
    private $config = [
        'Author' => [
            'table'      => 'authors',
            'columns'    => [
                'id' => 'integer|unsigned|ai',
            ],
            'timestamps' => false,
        ],
        'Book'   => [
            'table'      => 'books',
            'columns'    => [
                'id'        => 'integer|unsigned|ai',
                'author_id' => 'fk',
            ],
            'timestamps' => false,
        ],
    ];

    public function testFromConfig(): void
    {
        $bag = ModelDefinitionsBag::fromConfig($this->config, true);

        $this->assertCount(2, $bag->getModelDefinitions(), 'count model definitions');
    }

    public function testForeignKeyLookup(): void
    {
        $bag      = ModelDefinitionsBag::fromConfig($this->config, true);
        $author   = $bag->getModelDefinition('Author');
        $authorPk = $author->getColumn('id');
        $book     = $bag->getModelDefinition('Book');
        $bookFk   = $book->getColumn('author_id');

        $this->assertCount(2, $bag->getModelDefinitions(), 'count model definitions');

        $this->assertSame('Author', $author->getModel(), 'author model name');
        $this->assertCount(1, $author->getColumns(), 'count author columns');

        $this->assertSame('Book', $book->getModel(), 'book model name');
        $this->assertCount(2, $book->getColumns(), 'count book columns');
        $this->assertSame($authorPk->dataType, $bookFk->dataType, 'dataType');
        $this->assertSame($authorPk->phpDataType, $bookFk->phpDataType, 'phpDataType');
        $this->assertSame($authorPk->unsigned, $bookFk->unsigned, 'unsigned');
        $this->assertSame($authorPk->length, $bookFk->length, 'length');
        $this->assertTrue($bookFk->foreignKey, 'foreignKey');
    }
}

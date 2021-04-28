<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Tests\Unit\Support;

use SimKlee\LaravelBakery\Support\Collection;
use SimKlee\LaravelBakery\Tests\Unit\UnitTestCase;

/**
 * Class CollectionTest
 * @package SimKlees\LaravelBakery\Tests\Unit\Support
 */
class CollectionTest extends UnitTestCase
{
    public function testExplode(): void
    {
        $collection = Collection::explode('one|two|three', '|');

        $this->assertCount(3, $collection);
        $this->assertSame([
            'one',
            'two',
            'three',
        ], $collection->toArray());
    }

    public function testSnake(): void
    {
        $collection = new Collection();
        $collection->add('one');
        $collection->add('two');
        $collection->add('three');

        $this->assertSame('one_two_three', $collection->snake());
    }

    public function testCamel(): void
    {
        $collection = new Collection();
        $collection->add('one');
        $collection->add('two');
        $collection->add('three');

        $this->assertSame('OneTwoThree', $collection->camel(true));
        $this->assertSame('oneTwoThree', $collection->camel(false));
    }

    public function testKebab(): void
    {
        $collection = new Collection();
        $collection->add('one');
        $collection->add('two');
        $collection->add('three');

        $this->assertSame('one-two-three', $collection->kebab());
    }
}

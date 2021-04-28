<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Tests\Unit\Models\Validation;

use PHPUnit\Framework\TestCase;
use SimKlee\LaravelBakery\Models\ColumnParser;
use SimKlee\LaravelBakery\Models\Validation\ColumnValidator;

/**
 * Class ColumnValidatorTest
 * @package SimKlees\LaravelBakery\Tests\Unit\Models\Validation
 */
class ColumnValidatorTest extends TestCase
{
    public function dataProviderForTestValidate(): array
    {
        return [
            [
                'name'        => 'StringColumn',
                'definition'  => 'varchar',
                'countErrors' => 0,
            ],
            [
                'name'        => 'StringColumn',
                'definition'  => 'char',
                'countErrors' => 0,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderForTestValidate
     *
     * @param string $name
     * @param string $definition
     * @param int    $countErrors
     */
    public function testValidate(string $name, string $definition, int $countErrors)
    {
        $validator = new ColumnValidator();
        $validator->validate(ColumnParser::parse($name, $definition));

        $this->assertCount($countErrors, $validator->getErrors());
    }
}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use Illuminate\Pipeline\Pipeline;
use SimKlee\LaravelBakery\Model\Column\Exceptions\ColumnComponentValidationException;
use SimKlee\LaravelBakery\Support\Collection;
use function PHPUnit\TestFixture\func;

/**
 * Class ColumnParser
 * @package SimKlee\LaravelBakery\Model\Column
 */
class ColumnParser
{
    private Column $column;

    /**
     * ColumnParser constructor.
     *
     * @param string $model
     * @param string $name
     * @param string $definition
     */
    public function __construct(string $model, string $name, string $definition)
    {
        $this->column = new Column($model, $name, Collection::explode('|', $definition));

        $this->parseDefinition();
    }

    private function parseDefinition(): void
    {
        app(Pipeline::class)
            ->send($this->column)
            ->through([
                ColumnDataType::class,
                ColumnAttributes::class,
                ColumnProperties::class,
                ColumnIndexes::class,
                ColumnForeignKeys::class,
            ]);

        $validator = new ColumnValidator($this->column);
        if ($validator->validate() === false) {
            $exception         = new ColumnComponentValidationException(
                'One or more ColumnValidatorErrors happened. For details look into error object!'
            );
            $exception->errors = $validator->getErrors();

            throw $exception;
        }
    }
}

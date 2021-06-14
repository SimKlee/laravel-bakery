<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use Illuminate\Pipeline\Pipeline;
use SimKlee\LaravelBakery\Model\Column\Exceptions\ColumnComponentValidationException;
use SimKlee\LaravelBakery\Support\Collection;

/**
 * Class ColumnParser
 * @package SimKlee\LaravelBakery\Model\Column
 */
class ColumnParser
{
    private Column $column;

    /**
     * @throws ColumnComponentValidationException
     */
    public function __construct(string $model, string $name, string $definition)
    {
        $this->column = new Column($model, $name, Collection::explode($definition, '|'));
        $this->parseDefinition();
    }

    /**
     * @throws ColumnComponentValidationException
     */
    public static function parse(string $model, string $name, string $definition): Column
    {
        $parser = new ColumnParser($model, $name, $definition);

        return $parser->getColumn();
    }

    /**
     * @throws ColumnComponentValidationException
     */
    private function parseDefinition(): void
    {
        collect([
            ColumnDataType::class,
            ColumnAttributes::class,
            ColumnProperties::class,
            ColumnIndexes::class,
            ColumnForeignKeys::class,
        ])->each(function (string $componentClass) {
            $instance = new $componentClass($this->column);
            $instance->handle($this->column);
        });

        // @TODO: activate Pipeline; $next in handle() is default null -> webmachen ;-)
        /*
        app(Pipeline::class)
            ->send($this->column)
            ->through([
                ColumnDataType::class,
                ColumnAttributes::class,
                ColumnProperties::class,
                ColumnIndexes::class,
                ColumnForeignKeys::class,
            ]);
        */

        $validator = new ColumnValidator($this->column);
        if ($validator->validate() === false) {
            dump($validator->getErrors());
            $exception         = new ColumnComponentValidationException(
                'One or more ColumnValidatorErrors happened. For details look into error object!'
            );
            $exception->errors = $validator->getErrors();

            throw $exception;
        }
    }

    /**
     * @return Column
     */
    public function getColumn(): Column
    {
        return $this->column;
    }
}

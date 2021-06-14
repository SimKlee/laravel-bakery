<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Model\Column\Validators\AbstractValidator;
use SimKlee\LaravelBakery\Model\Column\Validators\ColumnValidatorError;
use SimKlee\LaravelBakery\Model\Column\Validators\ImproperAutoIncrementValidator;
use SimKlee\LaravelBakery\Model\Column\Validators\ImproperNullableValidator;
use SimKlee\LaravelBakery\Model\Column\Validators\ImproperPrecisionValidator;
use SimKlee\LaravelBakery\Model\Column\Validators\ImproperUnsignedValidator;
use SimKlee\LaravelBakery\Model\Column\Validators\MissingPrecisionValidator;

/**
 * Class ColumnValidator
 * @package SimKlee\LaravelBakery\Model\Column
 */
class ColumnValidator
{
    private Column     $column;
    private Collection $errors;

    private array $validators = [
        ImproperAutoIncrementValidator::class,
        ImproperNullableValidator::class,
        ImproperPrecisionValidator::class,
        ImproperUnsignedValidator::class,
        MissingPrecisionValidator::class,
    ];

    /**
     * ColumnValidator constructor.
     *
     * @param Column $column
     */
    public function __construct(Column $column)
    {
        $this->column = $column;
        $this->errors = new Collection();
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $validated = collect($this->validators)->map(function (string $class) {
            $validator = AbstractValidator::factory($class, $this->column);
            $validator->validate();

            return $validator;
        });

        $this->errors = $validated->filter(function (AbstractValidator $validator) {
            return $validator->getError() instanceof ColumnValidatorError;
        })->map(function (AbstractValidator $validator) {
            return $validator->getError();
        });

        return $this->errors->filter(function (AbstractValidator $validator) {
                return $validator->getError()->level === AbstractValidator::LEVEL_ERROR;
            })->count() === 0;
    }

    /**
     * @return Collection|ColumnValidatorError[]
     */
    public function getErrors(): Collection
    {
        return $this->errors;
    }


}

<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\Exceptions;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Model\Column\Validators\ColumnValidatorError;

/**
 * Class ColumnComponentValidationException
 * @package SimKlee\LaravelBakery\Model\Column\Exceptions
 */
class ColumnComponentValidationException extends \Exception
{
    public Collection $errors;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->errors->map(function (ColumnValidatorError $error) {
            return sprintf('[%s] %s', $error->level, $error->message);
        })->implode(PHP_EOL);
    }
}

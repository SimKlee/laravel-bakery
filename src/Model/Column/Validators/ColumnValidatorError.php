<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column\Validators;

/**
 * Class ColumnValidatorError
 * @package SimKlee\LaravelBakery\Model\Column\Validators
 */
class ColumnValidatorError
{
    public string $level;
    public string $message;

    /**
     * ColumnValidatorError constructor.
     *
     * @param $level
     * @param $message
     */
    public function __construct($level, $message)
    {
        $this->level   = $level;
        $this->message = $message;
    }
}

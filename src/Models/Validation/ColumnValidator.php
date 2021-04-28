<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Models\Validation;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Models\Column;

/**
 * Class ColumnValidator
 * @package SimKlees\LaravelBakery\Models\Validation
 */
class ColumnValidator
{
    private $expectedAttributesByPhpDataType = [
        'string' => [
            'autoIncrement' => false,
            'unsigned'      => false,
        ],
        'int'    => [],
    ];

    /**
     * @var Collection
     */
    private $errorBag;

    public function __construct()
    {
        $this->errorBag = new Collection();
    }

    /**
     * @param Column $column
     *
     * @return bool
     */
    public function validate(Column $column): bool
    {
        $checked = true;
        if (!$this->checkExpectedAttributes($column)) {
            $checked = false;
        }

        return $checked;
    }

    private function checkExpectedAttributes(Column $column): bool
    {
        $checked    = true;
        $attributes = $this->expectedAttributesByPhpDataType[ $column->phpDataType ];
        foreach ($attributes as $attribute => $value) {
            if ($column->$attribute !== $value) {
                $this->errorBag->add(new ValidationError());
                $checked        = false;
            }
        }

        return $checked;
    }

    /**
     * @return Collection
     */
    public function getErrors(): Collection
    {
        return $this->errorBag;
    }
}

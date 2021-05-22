<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

use SimKlee\LaravelBakery\Model\Exceptions\WrongAttributeException;

/**
 * Class ColumnAttributes
 * @package SimKlee\LaravelBakery\Model\Column
 */
class ColumnAttributes extends AbstractColumnComponent
{
    const ATTRIBUTE_AUTO_INCREMENT = 'ai';
    const ATTRIBUTE_NULLABLE       = 'nullable';
    const ATTRIBUTE_UNSIGNED       = 'unsigned';

    protected function parseDefinitions(): void
    {
        $this->column->definitions->each(function (string $item) {
            switch ($item) {
                case self::ATTRIBUTE_AUTO_INCREMENT:
                    if ($this->column->phpDataType !== 'int') {
                        throw new WrongAttributeException('Wrong attribute "autoIncrement" for data type ' . $this->column->dataType);
                    }
                    $this->column->autoIncrement = true;
                    $this->column->primaryKey    = true;
                    break;

                case self::ATTRIBUTE_UNSIGNED:
                    if ($this->column->phpDataType !== 'int') {
                        throw new WrongAttributeException('Wrong attribute "unsigned" for data type ' . $this->column->dataType);
                    }
                    $this->column->unsigned = true;
                    break;

                case self::ATTRIBUTE_NULLABLE:
                    if ($this->column->autoIncrement) {
                        throw new WrongAttributeException('Column cannot be nullable for auto increment!');
                    }
                    $this->column->nullable = true;
                    break;
            }
        });
    }
}

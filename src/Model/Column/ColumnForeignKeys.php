<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Model\Column;

/**
 * Class ColumnForeignKeys
 * @package SimKlee\LaravelBakery\Model\Column
 */
class ColumnForeignKeys extends AbstractColumnComponent
{
    const FOREIGN_KEY_ON_UPDATE = 'onupdate';
    const FOREIGN_KEY_ON_DELETE = 'ondelete';

    const FOREIGN_KEY_CASCADE   = 'cascade';
    const FOREIGN_KEY_RESTRICT  = 'restrict';
    const FOREIGN_KEY_NO_ACTION = 'no action';
    const FOREIGN_KEY_SET_NULL  = 'set null';

    protected function parseDefinitions(): void
    {
        $this->column->definitions->each(function (string $item) {
            if (strpos($item, ':')) {
                [$attribute, $value] = explode(':', $item);

                if (!in_array($attribute, [self::FOREIGN_KEY_ON_UPDATE, self::FOREIGN_KEY_ON_DELETE])) {
                    return true;
                }

                if (!in_array($value, [self::FOREIGN_KEY_CASCADE, self::FOREIGN_KEY_RESTRICT, self::FOREIGN_KEY_NO_ACTION, self::FOREIGN_KEY_SET_NULL])) {
                    throw new \Exception(sprintf('Unknown value "%s"', $value));
                }

                switch ($attribute) {
                    case self::FOREIGN_KEY_ON_UPDATE:
                        $this->column->foreignKeyOnUpdate = $value;
                        break;

                    case self::FOREIGN_KEY_ON_DELETE:
                        $this->column->foreignKeyOnDelete = $value;
                        break;
                }
            }
        });
    }
}

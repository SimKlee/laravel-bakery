<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Generator\Formatter\Model;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Generator\Formatter\AbstractFormatter;
use SimKlee\LaravelBakery\Model\Column\Column;
use SimKlee\LaravelBakery\Model\ModelRelation;

/**
 * Class ClassMethodStrings
 * @package SimKlee\LaravelBakery\Generator\Formatter\Model
 */
class ClassMethodStrings extends AbstractFormatter
{
    public function toString(): string
    {
        $methods = new Collection();
        if ($this->modelDefinition->useUuid) {
            $methods->add(sprintf(' * @method static %s findByUuid(string $uuid)', $this->modelDefinition->model));
        }

        return $methods->implode(PHP_EOL);
    }

}

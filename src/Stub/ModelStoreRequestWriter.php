<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use Illuminate\Support\Collection;
use SimKlee\LaravelBakery\Models\Column;
use SimKlee\LaravelBakery\Models\ModelDefinition;
use Str;

/**
 * Class ModelStoreRequestWriter
 * @package SimKlee\LaravelBakery\Support
 */
class ModelStoreRequestWriter extends Stub
{
    /**
     * @var ModelDefinition
     */
    private ModelDefinition $modelDefinition;

    /**
     *
     * @param ModelDefinition $modelDefinition
     *
     * @return ControllerWriter
     */
    public static function fromModelDefinition(ModelDefinition $modelDefinition): ControllerWriter
    {
        $writer = new ControllerWriter('mode_store_request.stub',);
        $writer->setModelDefinition($modelDefinition);

        return $writer;
    }

    /**
     * @param ModelDefinition $modelDefinition
     */
    public function setModelDefinition(ModelDefinition $modelDefinition): void
    {
        $this->modelDefinition = $modelDefinition;
    }

    /**
     * @param string $file
     * @param bool   $override
     *
     * @return bool|int
     */
    public function write(string $file, bool $override = false)
    {
        $this->replace('Model', $this->modelDefinition->getModel());

        return parent::write($file, $override);
    }
}

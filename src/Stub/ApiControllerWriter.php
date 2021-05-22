<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Stub;

use SimKlee\LaravelBakery\Models\ModelDefinition;

/**
 * Class ApiControllerWriter
 * @package SimKlee\LaravelBakery\Support
 */
class ApiControllerWriter extends Stub
{
    /**
     * @var ModelDefinition
     */
    private ModelDefinition $modelDefinition;

    /**
     *
     * @param ModelDefinition $modelDefinition
     *
     * @return ApiControllerWriter
     */
    public static function fromModelDefinition(ModelDefinition $modelDefinition): ApiControllerWriter
    {
        $writer = new ApiControllerWriter('api_controller.stub',);
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
        $this->replace('Model', $this->modelDefinition->getModel())
             ->replace('model', $this->modelDefinition->getModel(true));

        return parent::write($file, $override);
    }
}

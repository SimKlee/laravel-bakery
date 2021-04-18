<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class InstallBlogPackage
 * @package SimKlee\LaravelBakery\Console\Commands
 */
class BakeModelCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'bake:model';

    /**
     * @var string
     */
    protected $description = 'Bake a new model.';

    public function handle(): void
    {

    }
}

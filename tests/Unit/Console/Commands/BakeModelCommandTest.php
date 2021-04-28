<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use SimKlee\LaravelBakery\Tests\TestCase;

/**
 * Class BakeModelCommandTest
 * @package SimKlee\LaravelBakery\Tests\Console\Commands
 */
class BakeModelCommandTest extends TestCase
{
    /** @test */
    function the_sample_option_creates_a_config_file_with_example()
    {
        $file = config_path('test_example.php');
        // make sure we're starting from a clean state
        if (File::exists($file)) {
            File::delete($file);
        }

        #$this->assertFalse(File::exists($file), 'file does not exists');

        $command = Artisan::call('bake:model', ['--sample' => true]);
        $command->expectsOutput('Here');

        $this->assertTrue(File::exists($file), 'file exists');
        // @todo aufräumen
    }
}

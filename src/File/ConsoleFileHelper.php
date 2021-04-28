<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\File;

use File;
use Illuminate\Console\Command;

class ConsoleFileHelper
{
    /**
     * @var Command
     */
    private $command;

    /**
     * ConsoleFileHelper constructor.
     *
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * @param string|null $file
     *
     * @return string
     */
    public static function getResourcePath(string $file = null): string
    {
        $path = __DIR__ . '/../../resources/';
        if (!is_null($file)) {
            $path .= $file;
        }

        return $path;
    }

    /**
     * @param string $path
     * @param string $contents
     * @param bool   $lock
     *
     * @return bool|int
     */
    public function put(string $path, string $contents, bool $lock = false)
    {
        $overwriteIfExists = true;
        if (File::exists($path)) {
            $overwriteIfExists = $this->command->choice(
                    sprintf('File "%s" already exists. Overwrite?', $path),
                    ['y' => 'yes', 'n' => 'no'],
                    'n'
                ) === 'y';
        }
        if ($overwriteIfExists) {
            return File::put($path, $contents, $lock);
        }

        return false;
    }
}

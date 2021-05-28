<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Console\Commands;

use File;
use Illuminate\Console\Command;
use SimKlee\LaravelBakery\Providers\LaravelBakeryServiceProvider;

/**
 * Class Install
 * @package SimKlee\LaravelBakery\Console\Commands
 *
 * @see     https://stillat.com/blog/2016/12/03/custom-command-styles-with-laravel-artisan
 */
class InstallBakery extends Command
{
    const OPTION_FORCE = 'force';

    /**
     * @var string
     */
    protected $signature = 'bake:install {--force : Override existing files.}';

    /**
     * @var string
     */
    protected $description = 'Install Laravel Bakery.';

    /**
     * @return int
     */
    public function handle(): int
    {
        $this->publishConfig();
        $this->createDirectories();
        $this->publishClasses();

        $this->publishViewComponents();
        $this->info('Datepicker View Component has some dependencies:');
        $this->info('Run: npm install bootstrap-datepicker --save');
        $this->info("Add to resources/sass/app.scss: @import '~bootstrap-datepicker/dist/css/bootstrap-datepicker.css';");
        $this->info("Add to resources/js/app.js: import 'bootstrap-datepicker/js/bootstrap-datepicker';");
        /*
         * @TODO: find a good way for the dependencies!!
        $(function () {
            $.fn.datepicker.dates['de'] = {
                days:        ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
                daysShort:   ['Son', 'Mon', 'Die', 'Mit', 'Don', 'Fre', 'Sam'],
                daysMin:     ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
                months:      ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
                monthsShort: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
                today:       new Date(),
            };
        });
         */
        $this->info('Run: npm run dev');

        $this->publishValidationRules();

        return 0;
    }

    private function publishConfig(): void
    {
        $this->info('Publishing package config...');
        $arguments = ['--tag' => 'config'];
        if ($this->option(self::OPTION_FORCE)) {
            $arguments['--force'] = true;
        }
        $this->call('vendor:publish', $arguments);
    }

    private function createDirectories(): void
    {
        $this->info(PHP_EOL);
        $this->info('Creating directories...');
        collect(LaravelBakeryServiceProvider::createDirectories())->each(function (string $line) {
            $this->info($line);
        });
    }

    private function publishClasses(): void
    {
        $this->info(PHP_EOL);
        $this->info('Publishing package classes...');
        $arguments = ['--tag' => 'classes'];
        if ($this->option(self::OPTION_FORCE)) {
            $arguments['--force'] = true;
        }
        $this->call('vendor:publish', $arguments);
    }

    private function publishViewComponents(): void
    {
        $this->info(PHP_EOL);
        $this->info('Publishing view components...');
        $arguments = ['--tag' => 'view_components'];
        if ($this->option(self::OPTION_FORCE)) {
            $arguments['--force'] = true;
        }
        $this->call('vendor:publish', $arguments);
    }

    private function publishValidationRules(): void
    {
        $this->info(PHP_EOL);
        $this->info('Publishing validation rules...');
        $arguments = ['--tag' => 'rules'];
        if ($this->option(self::OPTION_FORCE)) {
            $arguments['--force'] = true;
        }
        $this->call('vendor:publish', $arguments);
    }
}

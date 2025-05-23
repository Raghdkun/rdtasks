<?php

namespace Imanghafoori\LaravelMicroscope\Features\CheckFacadeDocblocks;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Imanghafoori\LaravelMicroscope\ErrorReporters\ErrorPrinter;
use Imanghafoori\LaravelMicroscope\ForPsr4LoadedClasses;
use Imanghafoori\LaravelMicroscope\Traits\LogsErrors;

class CheckFacadeDocblocks extends Command
{
    use LogsErrors;

    protected $signature = 'check:facades {--f|file=} {--d|folder=}';

    protected $description = 'Checks facade doc-blocks';

    public function handle()
    {
        event('microscope.start.command');
        $this->info('Checking Facades...');

        $errorPrinter = ErrorPrinter::singleton($this->output);

        Event::listen('microscope.facade.docblocked', function ($class) {
            $this->line('- Fixed doc-blocks for: "'.$class.'"', 'fg=yellow');
        });

        Event::listen('microscope.facade.accessor_error', function ($accessor, $absFilePath) {
            ErrorPrinter::singleton()->simplePendError('"'.$accessor.'"', $absFilePath, 20, 'asd', 'The Facade Accessor Not Found.');
        });

        $fileName = ltrim($this->option('file'), '=');
        $folder = ltrim($this->option('folder'), '=');

        $results = ForPsr4LoadedClasses::check([FacadeDocblocks::class], [], $fileName, $folder);
        iterator_to_array($results);

        $errorPrinter->printTime();
        $errorPrinter->logErrors();

        return $errorPrinter->hasErrors() ? 1 : 0;
    }
}

<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Console\ServeCommand;
use Tests\TestCase;

class ServeCommandTest extends TestCase
{
    public function test_artisan_serve_keeps_the_temporary_folder_for_uploads(): void
    {
        // Without TEMP and TMP, PHP on Windows has nowhere to put an upload while it arrives.
        $this->assertContains('TEMP', ServeCommand::$passthroughVariables);
        $this->assertContains('TMP', ServeCommand::$passthroughVariables);
    }
}

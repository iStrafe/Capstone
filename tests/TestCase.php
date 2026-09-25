<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Tests don't build frontend assets, so skip the Vite manifest lookup.
        $this->withoutVite();
    }
}

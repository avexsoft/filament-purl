<?php

namespace Avexsoft\FilamentPurl\Tests;

use Orchestra\Testbench\TestCase as Testbench;

abstract class TestbenchTestCase extends Testbench
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
       parent::tearDown();
    }

    protected function getPackageProviders($app): array
    {
        return [
            \Avexsoft\FilamentPurl\FilamentPurlServiceProvider::class
        ];
    }
}

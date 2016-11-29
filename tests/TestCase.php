<?php

use Orchestra\Testbench\TestCase as TestBenchTestCase;

class TestCase extends TestBenchTestCase
{
    public function setUp()
    {
        parent::setUp();
    }
    
    protected function getPackageProviders($app)
    {
        return [Ultraware\Roles\RolesServiceProvider::class];
    }
}

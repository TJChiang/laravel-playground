<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends BaseTestCase
{
    protected $connectionsToTransact = ['mysql', 'mysql_test'];

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('database.default', 'mysql_test');
    }
}

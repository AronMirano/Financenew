<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        // Force testing configuration
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('app.env', 'testing');
        $app['config']->set('app.debug', true);
        $app['config']->set('app.url', 'http://localhost');
        $app['config']->set('session.driver', 'array');
        $app['config']->set('cache.store', 'array');
        $app['config']->set('queue.connection', 'sync');

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations on the sqlite database
        $this->artisan('migrate', ['--force' => true]);
    }
}

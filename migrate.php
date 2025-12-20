<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Set the environment to 'testing'
$app->loadEnvironmentFrom('.env.testing');

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\StringInput('migrate'),
    new Symfony\Component\Console\Output\ConsoleOutput
);

exit($status);
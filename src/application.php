#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new App\Command\Encrypt);
$application->add(new App\Command\Decrypt);
$application->add(new App\Command\RemoveOldEmail);


$application->run();
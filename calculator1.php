<?php



require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use calculator\command\CalculatorCommand;


$application = new Application();



$application->add(new CalculatorCommand());

$application->run();
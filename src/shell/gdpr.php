<?php

use Iresults\Gdpr\Model\Shell\Command;
use Iresults\Shell\Application;

@include_once __DIR__ . '/../../../Iresults_Shell/src/app/code/local/Iresults/Shell/autoload.php';
@include_once __DIR__ . '/../../../../app/code/local/Iresults/Shell/autoload.php';

$application = new Application();
error_reporting(E_ALL);
$application
    ->add(Command\Customer\ShowCommand::class)
    ->add(Command\Sales\Order\DeleteCommand::class)
    ->add(Command\Sales\Order\ListCommand::class)
    ->add(Command\Sales\Order\ShowCommand::class)
    ->add(Command\Sales\Order\DeleteAllCommand::class)
    ->run($argv);


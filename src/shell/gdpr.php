<?php

use Iresults\Shell\Application;

@include_once __DIR__ . '/../../../Iresults_Shell/src/app/code/local/Iresults/Shell/autoload.php';
@include_once __DIR__ . '/../../../../app/code/local/Iresults/Shell/autoload.php';

$application = new Application();
error_reporting(E_ALL);
$application
    ->add(Iresults_Gdpr_Model_Shell_Command_Customer_Show::class)
    ->run($argv);


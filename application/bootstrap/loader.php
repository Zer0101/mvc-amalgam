<?php

//Fetch custom namespaces, classmap and files
try{
    $registeredFiles = include_once APPPATH . 'config/autoload.php';
} catch (\Exception $e){
    $registeredFiles = [];
}

//Initialize autoloader
$loader = new \Amalgam\Autoload\Loader();
$loader->registerNamespaces($registeredFiles['psr4']);
$loader->registerClasses($registeredFiles['classmap']);
$loader->registerFiles($registeredFiles['files']);
$loader->register();

//Connect composer to project
require_once BASEPATH . 'vendor/autoload.php';
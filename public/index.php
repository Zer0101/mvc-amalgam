<?php

//Define standard paths
define("FCPATH" , realpath(__DIR__) . '/');
define("BASEPATH", dirname(FCPATH) . '/');
define("APPPATH", BASEPATH . 'application/');
define("SYSPATH", BASEPATH . 'system/');

try {
    //Load bootstrap files
    require_once SYSPATH . 'bootstrap.php';
    require_once APPPATH . 'bootstrap/loader.php';
    require_once APPPATH . 'bootstrap/services.php';

} catch (\Exception $e) {
    echo $e->getMessage();
}
<?php

function autoloadLibrary($file){
    
    $parts = explode('\\',$file);
    $path = realpath(__DIR__.'/library/'.implode('/',$parts).'.php');

    if(file_exists($path)){
        require $path;
        return true;
    } else {
        return false;
    }
}

spl_autoload_register('autoloadLibrary');

require '/usr/share/php/PHPUnit/Autoload.php';

define('BUTLER_PATH_TESTS','/var/www/tests/');

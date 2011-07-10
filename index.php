<?php

require 'bootstrap.php';

$run = '';
$reload = isset($_SESSION['reload']) && $_SESSION['reload'] == true ? 'checked="checked"' : '';

$error_msg = array();

if(version_compare(PHP_VERSION,'5.3') == -1){
    $error_msg[] = 'PHPUnit Butler requires php version 5.3 or newer.';
}

if(!function_exists('xdebug_memory_usage')){
    $error_msg[] = 'Xdebug is not installed.';
}

if(!file_exists(BUTLER_PATH_PHPUNIT.'/Autoload.php')){
    $error_msg[] = 'BUTLER_PATH_PHPUNIT is not set correctly. Should be path to PHPUnit (version 3 or newer).';
}

if(!is_dir(BUTLER_PATH_TESTS)){
    $error_msg[] = 'BUTLER_PATH_TESTS is not set correctly. Should be path to directory with unit tests.';
}
   
include 'views/index.php';
<?php
require 'autoload.php';

if(function_exists('spl_autoload_register')){
    spl_autoload_register('autoloadLibrary');
}

// Folder where the Butler can find unit tests
define('BUTLER_PATH_TESTS', __DIR__.'/tests');


// Path to PHPUnit
define('BUTLER_PATH_PHPUNIT','/usr/share/php/PHPUnit');

//Comma separated string with dirs and files that the Butler should ignore
define('BUTLER_PATH_IGNORE','bootstrap');

//Define how many tests to show when the search box is empty. Default to the latest 10 tests
define('BUTLER_LATEST_SIZE',10);

// The interval between each reload of the test if the 'Auto reload' option is checked.
// The Butler will only reload a test if it has changed.
define('BUTLER_RELOAD_INTERVAL',2000);

session_start();
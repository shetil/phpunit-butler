<?php
require 'bootstrap.php';
require BUTLER_PATH_PHPUNIT.'/Autoload.php';

$runner = new Butler\Runner($_GET['test']);
$success = $runner->run();


if($success == true){
    $stats = $runner->getStatistics();
    include 'views/testresult.php';
}
?>
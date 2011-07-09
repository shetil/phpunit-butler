<?php
require 'bootstrap.php';

$test = $_GET['test'];

$arrTest = explode('/',$test);
$class = array_pop($arrTest);
$path  = implode('/',$arrTest);

$suiteResults = array();

require BUTLER_PATH_TESTS.$path.'/'.$class.'.php';

$listener = new Butler\Listener;

$result = new PHPUnit_Framework_TestResult();
//$result->collectCodeCoverageInformation(true);
$result->addListener($listener);

if(is_subclass_of($class, 'PHPUnit_Framework_TestSuite')){
    $suite = $class::suite();
} else {
    $suite = new PHPUnit_Framework_TestSuite($class);
}

$result = $suite->run($result);

include 'views/testresult.php';
?>
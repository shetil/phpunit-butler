<?php
require 'bootstrap.php';
require BUTLER_PATH_PHPUNIT.'/Autoload.php';

$start_time = microtime(true);
$test = trim($_GET['test']);


//Reload request. Run current test if file has changed.
if($test == '[reload]' && isset($_SESSION['current'])){
    $current = $_SESSION['current'];
    
    if(file_exists($current['filename'])){
        $stat = stat($current['filename']);
    
        if($stat['mtime'] > $current['mtime']){
            $test = $current['name'];
        } else {
            exit;
        }
        
    } else {
        exit;
    }
}

//Get the full path to the testfile
$arrTest = explode('/',$test);
$class = array_pop($arrTest);
$path  = implode('/',$arrTest);

$suiteResults = array();
$testfile = BUTLER_PATH_TESTS."/$path/$class.php";


//Search for bootstrap file in the testfile path
$bootstrap = null;
$curPath = BUTLER_PATH_TESTS;
do{

   if(file_exists($curPath."/bootstrap.php")){
       $bootstrap = $curPath."/bootstrap.php";
   } 
   
   $curPath .= '/'.array_shift($arrTest);
   
}while(sizeof($arrTest) > 0 && $bootstrap == null);

if($bootstrap){
    include $bootstrap;
}

//$_SESSION['latest'] contains a list of the test that has been run before
if(isset($_SESSION['latest'])){

    //Remove current test from list of latest tests
    $index = array_search($test,$_SESSION['latest']);
    if($index !== false){
        unset($_SESSION['latest'][$index]);
    }

} else{

    $_SESSION['latest'] = array();

}


//Include the testfile
if(file_exists($testfile)){
 
    //Store the test as the first entry in list
    array_unshift($_SESSION['latest'], $test);
    
    //Store testfile stats as 'current' in session
    $stats = stat($testfile);
    $_SESSION['current'] =  array(
        'name'=>$test,
        'filename'=>$testfile,
        'mtime'=>$stats['mtime']
    );
    
    require $testfile;

//Display error if testfile is not found    
} else { 
    
    $error_msg =  'Could not find test file: '.$testfile;
    include 'views/testresult.php';
    exit;
    
}

$listener = new Butler\Listener;

$result = new PHPUnit_Framework_TestResult();
$result->addListener($listener);

if(is_subclass_of($class, 'PHPUnit_Framework_TestSuite')){
    $suite = $class::suite();
} else {
    $suite = new PHPUnit_Framework_TestSuite($class);
}

//Run test!
$result = $suite->run($result);

//Collect statistics
$total_time  = microtime(true) - $start_time;
$count_total = $result->count();

$count_failed = $result->errorCount() + $result->failureCount();
$pst_failed = round(($count_failed/$count_total)*100);

$count_passed = sizeof($result->passed());
$pst_passed = round(($count_passed/$count_total)*100);

$count_skipped = $result->skippedCount() + $result->notImplementedCount();
$pst_skipped = round(($count_skipped/$count_total)*100);

include 'views/testresult.php';
?>
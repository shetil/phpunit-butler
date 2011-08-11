<?php
namespace Butler;

class Runner {

    protected $_starttime;
    protected $_endtime;
    protected $_runtime;
    protected $results  = array();
    
    /**
     *
     * @var PHPUnit_Framework_TestSuite
     */
    protected $_test;
   
    /**
     *
     * @var Reload
     */
    protected $_reload = null;
    
    /**
     *
     * @var \PHPUnit_Framework_TestResult
     */
    protected $_result = null;
    
    public function __construct($test){
        
        $this->_test = trim($test);
        
        if($this->_test == '[reload]'){
            $this->_reload = new Reload();
        }
    }
    
    public function isReload(){
        return is_object($this->_reload);
    }
    
    /**
     *
     * @return array
     */
    public function getResults(){
        return $this->results;
    }
    
    /**
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public function getSuite(){
        return $this->_suite;
    }
    
    public function addResult($name,$result){
        $this->results[$name] = $result;
    }
    
    public function run() {

        
        if($this->isReload() == true){
            $current = $this->getCurrent();
            
            $this->_reload->setCurrent($current);
            
            if($this->_reload->isValid() == false){
                return false;
            }
            
            $this->_test = $current['test'];
        }

        $testinfo = $this->getTestInfo($this->_test);

        if(file_exists($testinfo['file'])){
            
            $this->updateLatest($this->_test);
            $this->updateCurrent($this->_test);
            
            $this->includeBootstrap($testinfo['path']);
            require $testinfo['file'];
            
        } else {
            $this->_error_msg = 'Could not find test file: '.$testinfo['file'];
            return false;
        }
        
                    
        if($this->isReload() == true){ 
             $this->_reload->setCurrent(array_merge($current,$testinfo));   
            
            if($this->_reload->isChanged() == false){
                return false;
            }
        }
        
        $this->_result = $this->initResult();
        $this->_suite = $this->initSuite($testinfo['class']);
        
        $this->_starttime = microtime(true);
        $this->_runtime = 123123;
        $this->_result = $this->_suite->run($this->_result);
        $this->_endtime = microtime(true);

        $this->_runtime = $this->_endtime - $this->_starttime;
        return true;
    }
   
    
    protected function initResult(){
        
        $listener = new Listener($this);

        $result = new \PHPUnit_Framework_TestResult();
        $result->addListener($listener);
        
        return $result;
    }
    
    protected function initSuite($class){

        if(is_subclass_of($class, '\PHPUnit_Framework_TestSuite')){
            $suite = $class::suite();
        } else {
            $suite = new \PHPUnit_Framework_TestSuite($class);
        }
        
        return $suite;
    }
    
    protected function includeBootstrap(array $path){

        $bootstrap = null;
        $curPath = BUTLER_PATH_TESTS;
        do{

           if(file_exists($curPath."/bootstrap.php")){
               $bootstrap = $curPath."/bootstrap.php";
           } 

           $curPath .= '/'.array_shift($path);

        }while(sizeof($path) > 0 && $bootstrap == null);

        if($bootstrap){
            include $bootstrap;
        }
    }
    
    protected function getTestInfo($test){
        
        $arrTest = explode('/',$test);
        $class = array_pop($arrTest);
        $path = implode('/',$arrTest);
        $fullpath = BUTLER_PATH_TESTS."/$path/$class.php";
        
        return array(
            'class'=>$class,
            'file'=>$fullpath,
            'path'=>$arrTest
        );
    }
    
    protected function updateLatest($test){

        if(isset($_SESSION['latest'])){

            //Remove current test from list of latest tests
            $index = array_search($test,$_SESSION['latest']);
            if($index !== false){
                unset($_SESSION['latest'][$index]);
            }

        } else{

            $_SESSION['latest'] = array();

        }

        array_unshift($_SESSION['latest'], $test);
    }
    
    protected function updateCurrent($test){
        
        $_SESSION['current_test'] = array(
            'test'=>$test,
            'latest_run'=>time()
        );
        
    }
    
    public function getCurrent(){
        
        if(isset($_SESSION['current_test'])){
            return $_SESSION['current_test'];
        } 
        
        return null;
    }
    
    public function getStatistics(){

        $statistics = array(
            'time'=>0,
            'total'=>0,
            'failed'=>0,
            'passed'=>0,
            'skipped'=>0
        );
        
        foreach($this->results as $result){
            $statistics['time'] += $result['runtime'];
            $statistics['total'] += $result['total'];
            $statistics['failed'] += $result['errors'] + $result['failure'];
            $statistics['passed'] += $result['success'];
            $statistics['skipped'] += $result['skipped'] + $result['incomplete'];
        }
        
        return $statistics;
    }
}

?>
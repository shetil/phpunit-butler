<?php

class util_Formatter implements PHPUnit_Framework_TestListener {
	private $results = array();
        private $errors = 0;
        private $incomplete = 0;
        private $success = 0;
        private $failure = 0;
        private $skipped = 0;
        private $total = 0;
        private $test_success;
        private $pre_mem;

	public function __construct() {
	}

        public function reset(){
            $this->results = array();
            $this->errors = 
            $this->incomplete  = 
            $this->success  = 
            $this->failure = 
            $this->skipped = 
            $this->total = 0;
        }

	public function getResults() {
            return $this->results;
	}

	public function startTestSuite( PHPUnit_Framework_TestSuite $suite) {

	}

	public function startTest(PHPUnit_Framework_Test $test) {
            $this->total++;
            $this->test_success = true;
            $this->pre_mem = xdebug_memory_usage();
	}

	public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time) {
            $this->results[] = new TestResult(TestResult::FAILURE, $test, $time, $this->calcMem(), $e);
            $this->failure++;
            $this->test_success = false;
	}
	
	public function addError(PHPUnit_Framework_Test $test, Exception $e, $time) {
            $this->results[] = new TestResult(TestResult::ERROR, $test, $time, $this->calcMem(), $e);
            $this->errors++;
            $this->test_success = false;
	}
	
	public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time) {
            $this->results[] = new TestResult(TestResult::INCOMPLETE, $test, $time, $this->calcMem(), $e);
            $this->incomplete++;
            $this->test_success = false;
	}

        public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time){
            $this->results[] = new TestResult(TestResult::SKIPPED, $test, $time, $this->calcMem(), $e);
            $this->skipped++;
            $this->test_success = false;
        }
    
    /**
	 * Upon completion of a test, records the execution time (if available) and adds the test to 
	 * the tests performed in the current suite.
	 * 
	 * @access public
	 * @param obj PHPUnit2_Framework_Test, current test that is being run
	 * @return void
	 */
	public function endTest( PHPUnit_Framework_Test $test, $time) {
            if($this->test_success === true){
                $mem = $this->calcMem();
                $this->results[] = new TestResult(TestResult::SUCCESS, $test,  $time, $mem);
                $this->success++;
            }

	}
	
	/**
	 * Upon completion of a test suite adds the suite to the suties performed
	 * 
	 * @acces public
	 * @param obj PHPUnit2_Framework_TestSuite, current suite that is being run
	 * @return void
	 */
	public function endTestSuite(PHPUnit_Framework_TestSuite $suite) {
            $GLOBALS['suiteResults'][$suite->getName()] = clone($this);
            $this->reset();
	}
        
    private function calcMem(){
       return xdebug_memory_usage() - $this->pre_mem;
    }
}

class TestResult{
    const ERROR = 0;
    const SUCCESS = 1;
    const INCOMPLETE = 2;
    const FAILURE = 3;
    const SKIPPED = 4;

    private $status_names = array(0=>'Error',1=>'Success',2=>'Incomplete',3=>'Failure',4=>'Skipped');

    public $name = null;
    public $time = 0;
    public $status = 0;
    public $exception = null;
    public $test = null;
    public $memory;

    public function __construct($status,$test,$time,$memory,$e=null){
        $name = preg_replace('(\(.*\))', '', $test->toString());
        $name = explode('::', $name);
        $this->name = array_pop($name);
        $this->time = $time;
        $this->status = $status;
        $this->exception = $e;
        $this->test = $test;
        $this->memory = $memory;
    }

    public function statusName(){
        return $this->status_names[$this->status];
    }

    public function formatMemory(){
        return round($this->memory/1000).' KB';
    }

    public function formatTime(){
       return number_format(round($this->time,3),3);
    }

    public function formatMessage(){

         if($this->exception){

            if($this->status == TestResult::ERROR){

                $msg = htmlspecialchars("{$this->exception->getMessage()} {$this->exception->getFile()} ({$this->exception->getLine()})");
                
                $msg = 'EXCEPTION';

            } else {

                 $trace = $this->exception->getTrace();
                 foreach($trace as $method){
                    if(!stristr($method['file'],'PHPUnit')){
                        break;
                    }
                 }
                 $msg = htmlspecialchars("{$this->exception->getMessage()} ({$method['line']})");
                 
            }

            return $msg;

        } else {

            return '&nbsp;';
        }

    }

    public function compactTrace(){

        $trace = $this->exception->getTrace();
        $str = '';

        foreach($trace as $method){

            if(isset($method['file'])
                    && !stristr($method['file'],'PHPUnit')
                    && isset($method['class'])
                    && !stristr($method['class'],'PHPUnit')
              ){
                $str .= "{$method['file']}({$method['line']}):\n\t {$method['class']}{$method['type']}{$method['function']}(";
                
                if(isset($method['args'])){
                    $args = array();
                    foreach($method['args'] as $arg){
                        if(is_object($arg)){
                            array_push($args, 'obj');
                        } elseif(is_array($arg)){
                            array_push($args, 'array');
                        } elseif(is_string($arg)){
                            array_push($args, "'".htmlspecialchars($arg)."'");
                        } else {
                            array_push($args, (string)$arg);
                        }
                    }

                    $str .= implode(',',$args);
                }

                $str .= ")\n";
            }
        }

        return $str;
    }
}

?>
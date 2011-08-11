<?php
namespace Butler;

class Listener implements \PHPUnit_Framework_TestListener {
	private $results = array();
    private $errors = 0;
    private $incomplete = 0;
    private $success = 0;
    private $failure = 0;
    private $skipped = 0;
    private $total = 0;
    private $test_success;
    private $pre_mem;
    private $coverage;
    private $startime;
    private $endtime;
    
	public function __construct() {
         require_once 'PHP/CodeCoverage.php';
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
    
    public function getResults(){
        return array(
            'errors'=>$this->errors,
            'incomplete'=>$this->incomplete,
            'success'=>$this->success,
            'failure'=>$this->failure,
            'skipped'=>$this->skipped,
            'total'=>$this->total,
            'runtime'=>$this->endtime - $this->startime,  
            'results'=>$this->results  
        );
    }

	public function startTestSuite(\PHPUnit_Framework_TestSuite $suite) {
        $this->startime = microtime(true);
	}

	public function startTest(\PHPUnit_Framework_Test $test) {
        $this->total++;
        $this->test_success = true;
        $this->pre_mem = xdebug_memory_usage();
	}

	public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time) {
        $this->results[] = new TestResult(TestResult::FAILURE, $test, $time, $this->calcMem(), $e);
        $this->failure++;
        $this->test_success = false;
	}
	
	public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time) {
        $this->results[] = new TestResult(TestResult::ERROR, $test, $time, $this->calcMem(), $e);
        $this->errors++;
        $this->test_success = false;
	}
	
	public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time) {
        $this->results[] = new TestResult(TestResult::INCOMPLETE, $test, $time, $this->calcMem(), $e);
        $this->incomplete++;
        $this->test_success = false;
	}

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time){
        $this->results[] = new TestResult(TestResult::SKIPPED, $test, $time, $this->calcMem(), $e);
        $this->skipped++;
        $this->test_success = false;
    }
    
    /**
	 * Upon completion of a test, records the execution time (if available) and adds the test to 
	 * the tests performed in the current suite.
	 * 
	 * @access public
	 * @param obj \PHPUnit2_Framework_Test, current test that is being run
	 * @return void
	 */
	public function endTest(\PHPUnit_Framework_Test $test, $time) {
        $mem = $this->calcMem();

        if($this->test_success === true){    
            $this->results[] = new TestResult(TestResult::SUCCESS, $test,  $time, $mem);
            $this->success++;
        }

	}
	
	/**
	 * Upon completion of a test suite adds the suite to the suites performed
	 * 
	 * @acces public
	 * @param obj \PHPUnit2_Framework_TestSuite, current suite that is being run
	 * @return void
	 */
	public function endTestSuite(\PHPUnit_Framework_TestSuite $suite) {
            $this->endtime = microtime(true);
            $GLOBALS['runner']->addResult($suite->getName(),$this->getResults());
            $this->reset();
	}
        
    private function calcMem(){
       return xdebug_memory_usage() - $this->pre_mem;
    }
}
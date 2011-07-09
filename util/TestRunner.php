<?php

class util_TestRunner {

    function __construct(PHPUnit_Framework_TestSuite $suite) {
        $this->suite = $suite;
    }

    function addFormatter(PHPUnit_Framework_TestListener $formatter) {
        $this->formatter = $formatter;
    }

    function run() {
        $result = new PHPUnit_Framework_TestResult();
        $result->addListener( $this->formatter );
        $this->suite->run($result);
        return $result;
    }
}

?>
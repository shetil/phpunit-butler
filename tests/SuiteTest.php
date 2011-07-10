<?php
require_once 'StatusTest.php';
require_once 'Subfolder/FilesTest.php';

class SuiteTest extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new SuiteTest();
        $suite->addTestSuite('StatusTest');
        $suite->addTestSuite('FilesTest');
        
        return $suite;
    }

    protected function setUp()
    {
//        print __METHOD__ . "\n";
    }

    protected function tearDown()
    {
//        print __METHOD__ . "\n";
    }
}


?>
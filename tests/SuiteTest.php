<?php
require_once 'StatusTest.php';
require_once '/var/www/sharefolder/beans/Blissio/Tests/Unit/AutoloadTest.php';

class SuiteTest extends PHPUnit_Framework_TestSuite
{
    public static function suite()
    {
        $suite = new SuiteTest();
        $suite->addTestSuite('StatusTest');
        $suite->addTestSuite('AutoloadTest');
        
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
<?php

class StatusTest extends \PHPUnit_Framework_TestCase {

    protected function setUp() {}

    protected function tearDown() {}

    public function testStatusSuccess() {
        $this->assertTrue(true);
    }

    public function testStatusFailure() {
        $this->assertTrue(false);
    }

    public function testStatusIncomplete(){
        $this->markTestIncomplete('Test message');
    }

    public function testStatusSkipped(){
        $this->markTestSkipped('Test message');
    }

    public function testUserError(){
        throw new Exception('This is a error');
    }

    public function testErrorInClass(){
        $cls = new stdClass();
        $cls->var = 5;
        new Butler\Error(1,$cls,Array(1,2,3));
    }
}

?>

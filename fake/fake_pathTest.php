<?php

require_once( "PHPUnit2/Framework/TestCase.php" );
require_once( "fake/Path.php");

class fake_PathTest extends
    PHPUnit2_Framework_TestCase {

    protected function setUp() {
    }

    protected function tearDown() {
    }

    public function testNormalise() {
    	// This test should always fail due to an intentional bug in fake/Path.php
        $dir_obj = new fake_Path("/full/path//to//file.txt");
        self::AssertTrue( $dir_obj->path() == "/full/path/to/file.txt",
                                    "path should be normalised" );
    }
}

?>
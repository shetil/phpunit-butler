<?php

class FilesTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Files
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Butler\Files(BUTLER_PATH_TESTS);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testGetFiles() {
        $files = $this->object->getFiles();
        
        $this->assertTrue($files['Subfolder'][0] == 'FilesTest');
    }

    public function testGetPaths() {
        $files = $this->object->getPaths();
        
        $this->assertTrue(in_array('Subfolder/FilesTest',$files));
    }

}

?>

<?php
require 'bootstrap.php';

$files = new Butler\Files('/var/www/tests');

$tests = $files->getPaths();

$result = array();
$strSearch = urldecode($_GET['term']);

foreach($tests as $test){

    if(stristr($test,$strSearch)){
        $result[] = $test;
    }

}

echo json_encode($result);

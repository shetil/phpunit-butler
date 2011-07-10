<?php
require 'bootstrap.php';

if(defined('BUTLER_PATH_IGNORE')){
    $ignore = explode(',',BUTLER_PATH_IGNORE);
} else {
    $ignore = null;
}

if(isset($_GET['q'])){
    $q = urldecode(trim($_GET['q']));
} else {
    $q = null;
}

if($q == '[latest]'){
    $result = array();
    
    if(isset($_SESSION['latest'])){
        $result = array_slice($_SESSION['latest'],0,BUTLER_LATEST_SIZE);
    }
    
    if(sizeof($result) < BUTLER_LATEST_SIZE){
        $files = new Butler\Files(BUTLER_PATH_TESTS,'',$ignore);
        $extra = $files->getPaths();
        $extra = array_slice($extra,0,BUTLER_LATEST_SIZE);
        
        foreach($extra as $entry){
            if(!in_array($entry,$result)){
                $result[] =$entry;
            }
        }
    }
    
   
} else {
    $files = new Butler\Files(BUTLER_PATH_TESTS,$q,$ignore);
    $result = $files->getPaths();
}

echo json_encode($result);

<?php

function autoloadLibrary($file){
    
    $parts = explode('\\',$file);
    $path = realpath(__DIR__.'/library/'.implode('/',$parts).'.php');

    if(file_exists($path)){
        require $path;
        return true;
    } else {
        return false;
    }
}
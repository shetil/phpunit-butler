<?php
namespace Butler;

class Error{

    public function __construct($value,$obj, array $arr) {
        new ErrorChild($value,$obj);
    }

}

class ErrorChild{
    
    public function __construct($value,$obj) {
        $sum = 1/0;
    }
    
}

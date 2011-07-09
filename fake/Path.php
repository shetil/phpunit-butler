<?php

class fake_Path {
    private $path;
    private $sep;

    function __construct( $path="." ) {
        $this->sep = DIRECTORY_SEPARATOR;
        $this->path = $this->normalise( $path );
    }

    function normalise( $pathstr ) {
        if($this->sep == '\\') $test = preg_replace('#['.$this->sep.$this->sep.']+#', $this->sep, $pathstr);
        else $test = preg_replace('['.$this->sep.']+', $this->sep, $pathstr);
        return $test;
   }

    function path() {
        return $this->path;
    }
}

?>
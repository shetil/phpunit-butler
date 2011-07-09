<?php
namespace Butler;

class Files{
    private $base = '';

    public function __construct($base){
        $this->base = $base;
    }

    public function getFiles(){
        return $this->findFiles($this->base);
    }

    public function getPaths(){
       $files = $this->findFiles($this->base);

       return $this->filesToPaths('',$files);
    }

    private function filesToPaths($base,$files){
       $output = array();

       foreach($files as $key=>$file){

           if(is_array($file) === true){
               $output = array_merge($output,$this->filesToPaths($base."$key/",$file));
           } else {
               array_push($output,$base.$file);
           }
       }

       return $output;
    }

    private function findFiles($base){
       $files = glob("$base/*.php");
       $dirs = glob("$base/*",GLOB_ONLYDIR);

       $output = array();

       foreach($dirs as $dir){
           $parts = explode(DIRECTORY_SEPARATOR,$dir);
           $output[array_pop($parts)] = $this->findFiles("$dir");
       }

       foreach($files as $file){
           $basename = basename($file,'.php');
           if($basename == 'bootstrap') continue;
           $output[] = $basename;
       }

       return $output;
    }

}

?>
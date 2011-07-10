<?php
namespace Butler;

class Files{
    private $_base = '';
    private $_ignore = array();
    private $_search = null;

    public function __construct($base,$search=null,array $ignore=null){
        $this->_base = $base;
        
        if($search){
            $this->_search = $search;
        }
        
        if(is_array($ignore)){
            $this->_ignore = $ignore;
        }
    }

    public function getFiles(){
        return $this->findFiles($this->_base);
    }

    public function getPaths(){
       $files = $this->findFiles($this->_base);

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
            $basename = basename($dir);
            
            if($this->includePath($basename)){
                $parts = explode(DIRECTORY_SEPARATOR,$dir);
                $output[array_pop($parts)] = $this->findFiles($dir);
           }
           
       }

       foreach($files as $file){
           $basename = basename($file,'.php');
           
           if($this->includePath($basename) && $this->search($file)){
               $output[] = $basename;
           }

       }

       return $output;
    }
    
    private function includePath($path){

        return !in_array($path,$this->_ignore);
        
    }
    
    private function search($file){

        return $this->_search == null || stristr($file, $this->_search);
        
    }

}

?>
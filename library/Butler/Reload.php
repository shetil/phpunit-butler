<?php
namespace Butler;

class Reload{
    
    protected $_current = array(
        'test'=>null,
        'class'=>null,
        'file'=>null,
        'latest_run'=>null
    );
    
    public function __construct(){

    }
    
    public function isValid(){
        return $this->_current['test'] != null;
    }
    
    public function setCurrent(array $current){
        $this->_current = $current;
    }
    
    public function isChanged(){

        $rcls = new \ReflectionClass($this->_current['class']);
        
        $classes = array_merge(
                $this->getDocMonitor($rcls),
                $this->getTestObject($rcls)
                );
        
        $rclasses = $this->getReflections($classes);
        
        $rclasses = $this->getParents($rclasses);
        array_unshift($rclasses, $rcls); 

        
        return $this->isClassChanged($rclasses);
    }

    public function getTestFilename(){
        return $this->_current['file'];
    }
    
    public function getLastRun(){
        return $this->_current['latest_run'];
    }
   
    protected function isClassChanged($classes){
        
        $checked = array();
        
        foreach($classes as $class){
            $file = $class->getFileName();
            $cls_name = $class->getName();
            
            if(in_array($cls_name,$checked) == false){
                if($this->isFileChanged($file)){
                    return true;
                }   
            }
            
            $checked[] = $cls_name;
        }
        
        return false;
    }
    
    protected  function getReflections($classes){
        
        $objects = array();

        foreach($classes as $class){
            $objects[] = new \ReflectionClass($class);
        }
        
        return $objects;
    }
    
    protected  function getParents($classes){
        
        $allcls = array();
        
        foreach($classes as $obj){
            
            $parent = $obj->getParentClass();
            if(is_object($parent)){
                $parents = $this->getParents(array($parent));
                $allcls[] = $parent;
                $allcls = array_merge($allcls,$parents);
            }
            
            $allcls[] = $obj;
        }
        
        
        return $allcls;
    }
    
    protected  function isFileChanged($file){
        
        $stat = stat($file);
        if($stat['mtime'] > $this->_current['latest_run']){
            return true;
        } else {
            return false;
        }
        
    }

    
    protected  function getDocMonitor(\ReflectionClass $rcls){ 
        $doccomment = $rcls->getDocComment();
        return $this->getDocElement('@monitor', $doccomment);
    }
    
    protected  function getTestObject(\ReflectionClass $rcls){
        
        try{
           
           $property = $rcls->getProperty('object');
           $doccomment = $property->getDocComment();
           return $this->getDocElement('@var',$doccomment);
           
        } catch(\Exception $e){
            
        }
        
        return array();
    }
    
    protected  function getDocElement($needle,$str){
        
        $matches = array();
        preg_match("/$needle (.*)\n/", $str, $matches);
        foreach($matches as &$match){
            $match = trim($match);
        }
        
        array_shift($matches);
        return $matches;
    }
    
}
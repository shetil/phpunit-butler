<?php
namespace Butler;

class TestResult{
    const ERROR = 0;
    const SUCCESS = 1;
    const INCOMPLETE = 2;
    const FAILURE = 3;
    const SKIPPED = 4;

    private $status_names = array('error','success','incomplete','failure','skipped');
    private $status_symbol = array('&times;','&bull;','-','&times;','-');
    
    public $name = null;
    public $time = 0;
    public $status = 0;
    public $exception = null;
    public $test = null;
    public $memory;
    


    public function __construct($status,$test,$time,$memory,$e=null){
        $name = preg_replace('(\(.*\))', '', $test->toString());
        $name = explode('::', $name);
        $this->name = array_pop($name);
        $this->time = $time;
        $this->status = $status;
        $this->exception = $e;
        $this->test = $test;
        $this->memory = $memory;
    }

    public function statusName(){
        return $this->status_names[$this->status];
    }
    
    public function statusSymbol(){
        return $this->status_symbol[$this->status];
    }

    public function formatMemory(){
        return round($this->memory/1000).' KB';
    }

    public function formatTime(){
       return number_format(round($this->time,3),3);
    }

    public function formatMessage(){

         if($this->exception){

            $msg = htmlspecialchars($this->exception->getMessage()); 
             
            if($this->status == TestResult::ERROR){

                
                $msg .= " (line: {$this->exception->getLine()})";
                
                $file = $this->exception->getFile();

            } else {

                 $trace = $this->exception->getTrace();
                 foreach($trace as $method){
                    if(!stristr($method['file'],'PHPUnit')){
                        break;
                    }
                 }
                 
                 $msg .= htmlspecialchars(" (line: {$method['line']})");
                 
                 $file = $method['file'];
                 
            }
           
            if($this->getTestFile() != $file){
                $msg .= "<br/><span class=\"file\">".$file."</span>";
            }

            return $msg;

        } else {

            return '&nbsp;';
        }

    }

    public function compactTrace(){

        $trace = $this->exception->getTrace();
        $str = '';

        foreach($trace as $method){

            if(isset($method['file'])
                    && !stristr($method['file'],'PHPUnit')
                    && isset($method['class'])
                    && !stristr($method['class'],'PHPUnit')
              ){
                
                
                if(isset($method['args'])){
                    $args = array();
                    foreach($method['args'] as $arg){
                        if(is_object($arg)){
                            array_push($args, get_class($arg));
                        } elseif(is_array($arg)){
                            array_push($args, 'array['.sizeof($arg).']');
                        } elseif(is_string($arg)){
                            array_push($args, "'".htmlspecialchars(substr($arg,0,20))."'");
                        } else {
                            array_push($args, (string)$arg);
                        }
                    }

                    $params = implode(', ',$args);
                }
                
                $str .= "<p>{$method['class']}{$method['type']}{$method['function']}($params)";
                
                $str .= "\n    <span class=\"file\">{$method['file']}({$method['line']})</span></p>";
            }
        }

        return $str;
    }
    
    public function getTestFile(){
       $reflection = new \ReflectionClass(get_class($this->test));
       return $reflection->getFileName();
    }
}
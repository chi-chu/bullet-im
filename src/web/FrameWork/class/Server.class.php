<?php
 Class Server {
     private $mcf;
     public function __construct($mcf) {
        $this->mcf = $mcf;
     }

     public function Start() {
         $class = '\\'.$this->mcf['m'].'\\'.$this->mcf['c'];
         $obj = new $class;
         $reflect = new ReflectionObject($obj);
         if ($reflect->hasMethod($this->mcf['f'])) {
             $reflect->getMethod($this->mcf['f'])->invoke($obj);
         } else {
             echo '<h1>未定义方法</h1>';
         }
     }
 }
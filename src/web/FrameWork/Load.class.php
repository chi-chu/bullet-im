<?php

defined('APP_START') or exit('Access Denied');
$CLASS_MAP = array();

class Load
{
    private $cache = array();

    private $usercache = array();

    public function __construct(){
        spl_autoload_register(function ($name) {
            if (DIRECTORY_SEPARATOR == '/') {
                $name = str_replace('\\', '/', $name);
            }else {
                $name = str_replace('/', '\\', $name);
            }
            if(file_exists(APP_CONTROL.DIRECTORY_SEPARATOR.$name.'.class.php')) {
                require APP_CONTROL.DIRECTORY_SEPARATOR.$name.'.class.php';
            }
            if(file_exists(APP_MODEL.DIRECTORY_SEPARATOR.$name.'.model.php')) {
                require APP_MODEL.DIRECTORY_SEPARATOR.$name.'.model.php';
            }
        });
    }

    function func($name) {
        if (isset($this->cache['func'][$name])) {
            return true;
        }
        $file = APP_FUNC.DIRECTORY_SEPARATOR . $name.'.php';
        if (file_exists($file)) {
            require $file;
            $this->cache['func'][$name] = true;
            return true;
        } else {
            trigger_error('Invalid Helper Function '.$file, E_USER_ERROR);
            return false;
        }
    }

    function model($name) {
        if (isset($this->cache['model'][$name])) {
            return true;
        }
        $file = APP_MODEL.DIRECTORY_SEPARATOR . $name . '.mod.php';
        if (file_exists($file)) {
            include $file;
            $this->cache['model'][$name] = true;
            return true;
        } else {
            trigger_error('Invalid Model '.$file, E_USER_ERROR);
            return false;
        }
    }

    function classes($name) {
        if (isset($this->cache['class'][$name])) {
            return true;
        }
        $file = APP_CLASS.DIRECTORY_SEPARATOR. $name . '.class.php';
        if (file_exists($file)) {
            include $file;
            $this->cache['class'][$name] = true;
            return true;
        } else {
            trigger_error('Invalid Class'.$file , E_USER_ERROR);
            return false;
        }
    }

    function extend($name) {
        if (isset($this->cache['extend'][$name])) {
            return true;
        }
        $file = APP_EXTEND.DIRECTORY_SEPARATOR. $name;
        if (file_exists($file)) {
            include $file;
            $this->cache['extend'][$name] = true;
            return true;
        } else {
            trigger_error('Invalid App Extend'.$file, E_USER_ERROR);
            return false;
        }
    }

    function c($name) {
        if (isset($this->usercache[$name])) {
            return $this->usercache[$name];
        }
        return new $name;
    }
}
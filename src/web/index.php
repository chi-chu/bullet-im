<?php
define('APP_VERSION', '1.0');

define('APP_START', true);

define('START_TIME', microtime());

define('APP_ROOT', dirname(__FILE__));
define('APP_CLASS', APP_ROOT.DIRECTORY_SEPARATOR.'FrameWork'.DIRECTORY_SEPARATOR.'class');
define('APP_FUNC', APP_ROOT.DIRECTORY_SEPARATOR.'FrameWork'.DIRECTORY_SEPARATOR.'function');
define('APP_EXTEND',APP_ROOT.DIRECTORY_SEPARATOR.'Extend');
define('APP_VIEW',APP_ROOT.DIRECTORY_SEPARATOR.'View');
define('APP_CONTROL', APP_ROOT.DIRECTORY_SEPARATOR.'Controller');
define('APP_MODEL', APP_ROOT.DIRECTORY_SEPARATOR.'Model');
define('MAGIC_QUOTES_GPC', (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) || @ini_get('magic_quotes_sybase'));

define('TIMESTAMP', time());

require APP_ROOT.DIRECTORY_SEPARATOR.'Config'.DIRECTORY_SEPARATOR.'config.php';
require APP_ROOT.DIRECTORY_SEPARATOR.'FrameWork'.DIRECTORY_SEPARATOR.'const.php';
require APP_ROOT.DIRECTORY_SEPARATOR.'FrameWork'.DIRECTORY_SEPARATOR.'Load.class.php';
require APP_ROOT.DIRECTORY_SEPARATOR.'FrameWork'.DIRECTORY_SEPARATOR.'init.php';


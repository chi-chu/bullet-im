<?php
defined('APP_START') or exit('Access Denied');

$_load = new Load();
global $_load;
$funcs = scandir(APP_FUNC);
foreach ($funcs as $val) {
    if ($val == '.' || $val == '..' ) {
        continue;
    }
    $_load->func(substr($val, 0, -4));
}

set_php_env($_CONFIG['setting']);
define('CLIENT_IP', getip());

$_load->classes('Server');
$svc = new Server(parseRequest());

$svc->Start();
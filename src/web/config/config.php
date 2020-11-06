<?php

defined('APP_START') or exit('Access Denied');
$_CONFIG = array(
    'db' => array(
        'engine' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'log',
        'charset' => 'utf8',
        'username' => 'root',
        'password' => '123456',
        'pconnect' => false,
    ),
    'cache' => array(
        'engine' => 'redis',
        'host' => '127.0.0.1',
        'port' => '6379',
        'auth' => '',
    ),
    'setting' => array(
        'memory_limit' => '256M',
    ),
    'app' => array(
        'image_limit' => array('gif', 'jpg', 'jpeg', 'png'),
        'image_size' => 10240,
    )
);

global $_CONFIG;
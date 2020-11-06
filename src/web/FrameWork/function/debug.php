<?php

function template($filename, $flag = 2) {

}

function message($msg, $redirect = '', $type = '') {
    var_dump($msg);
    template("", 2);
    exit();
}

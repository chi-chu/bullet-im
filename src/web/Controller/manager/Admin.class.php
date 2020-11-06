<?php

namespace manager;
class Admin
{
    public function __construct() {
        if (!$_SESSION['login']) {
            $this->index();
        }
    }

    public function Index() {
        //M("Admin");
        echo "<h1>hello  world</h1>";
    }

    public function login() {

    }
}
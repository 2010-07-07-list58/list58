<?php

if (!ini_get('display_errors')) {
    ini_set('display_errors', 1);
}
error_reporting(E_ALL);

require_once dirname(__FILE__).'/../class.main.ns17829.php';

$main = new main__ns17829();

$main->run();





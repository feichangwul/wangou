<?php
function load_file($name, $path = __DIR__)
{
    $class_file = $path . DIRECTORY_SEPARATOR . $name . '.php';
    require_once $class_file;
}
load_file('admin778899');
//include 'admin778899.php';
<?php 

spl_autoload_register(function ($class) {
    include 'classes/' . $class . '.php';
});

error_reporting(E_ALL);

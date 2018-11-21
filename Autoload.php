<?php

spl_autoload_register(function ($className) {
    $path = __DIR__;
    $className = str_replace('\\', '/', $className);
    $className = $path . '/' . $className . '.php';
    if (is_file($className)) {
        require_once($className);
    }
});

<?php

    require_once('Lib/Common/start.php');

    $c = isset($_GET['C']) ? $_GET['C'] : 'Base';//调用的控制器

    $m = isset($_GET['M']) ? $_GET['M'] : 'index';//控制器的方法

    $controller = ucwords($c) . 'Controller';

    //先判断控制器是否存在
    if (file_exists(c_path . $controller . '.class.php')) {

        //自动调用加载类
        if (class_exists($controller)) {

            $handle = new $controller();

            $method = $handle->$m();
        }else {

        }
    }else{

    }

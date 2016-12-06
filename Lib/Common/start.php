<?php
	@session_start();

	//error_reporting(0);
	
	header("Content-type:text/html;charset=utf-8");
	
	date_default_timezone_set('Asia/Shanghai');

    require_once (__DIR__.'/../Vendor/vendor/autoload.php');

	require_once(__DIR__.'/../Config/config.php');
	
	require_once(__DIR__.'/../Common/common.php');
		
    require_once(__DIR__.'/../Driver/errorHandle.php');

	function load($class_name){

		if(strstr($class_name, 'Controller') !== false){
			
			require_once(c_path.$class_name.'.class.php');
		
			return ;
		}
	}
	spl_autoload_register('load');


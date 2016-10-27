<?php
	@session_start();

	//error_reporting(0);
	
	header("Content-type:text/html;charset=utf-8");
	
	date_default_timezone_set('Asia/Shanghai');
	
	require_once(__DIR__.'/../Config/config.php');
	
	require_once(__DIR__.'/../Common/common.php');
		
	require_once(__DIR__.'/../Driver/errorHandle.driver.php');
	
	function load($class_name){
		
		// 控制器
		if(strstr($class_name, 'Controller') !== false){
			
			require_once(c_path.$class_name.'.class.php');
		
			return ;
		} 

		//返回系统驱动的类名数组
		$config = new config();
		
		$system_driver = $config::sysDriver();
		
		if( in_array($class_name, $system_driver)){
		
			echo $class_name;
		
			require_once(d_path.$class_name.'.driver.php');
		
			return;
		}

		
	}
	spl_autoload_register('load');


<?php
	
	$c = isset($_GET['C']) ? $_GET['C'] : 'Base';

	$m = isset($_GET['M']) ? $_GET['M'] : 'index';
	
	$controller = ucwords($c).'Controller';
	
	require_once('Lib/Common/start.php');
	
	if(file_exists(c_path.$controller.'.class.php')){

		if(class_exists($controller)){
	
			$handle = new $controller();
	
			$method = $handle->$m();
			
		}else{

			$back_info = array(
			
				'status'=>false,
	
				'error_code'=>-1000010,
	
				'error_msg'=>'invaildClass',
		
			);
			echo jsonEncode($back_info);
		}

	}else{

		$back_info = array(
			
			'status'=>false,
	
			'error_code'=>-1000010,
	
			'error_msg'=>'invaildClass',
		);
		echo jsonEncode($back_info);
	}
	

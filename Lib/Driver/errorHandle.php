<?php
set_error_handler('errorHandle');
register_shutdown_function('shutdownFunction');

function errorHandle($errno = NULL,$errstr = NULL,$errfile = NULL,$errline = NULL){
	
	if($errno != '8192'){
			$str  = 'errorHandle:'.PHP_EOL;
			$str  = "错误类型:".$errno.PHP_EOL;
			$str .= "错误提示:".$errstr.PHP_EOL;
			$str .= "错误文件:".$errfile.PHP_EOL;
			$str .= "错误行:".$errline.PHP_EOL;
			writeLog($str);
	}
}

function shutdownFunction(){
	
	$error = error_get_last();
	
	if($error['type'] != '8192' && isset($error)){
		$error['tag'] = 'shutdownFunction';
		writeLog($error);
	}
}
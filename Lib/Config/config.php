<?php
  
    //数据库基本配置
    define('DB_HOST', '127.0.0.1');
    define('DB_USR', 'root');
    define('DB_PWD','');
    define('DB_NAME', 'jrwb');
    define('DB_PORT', 3306);
   	
   	//文件目录
   	define('c_path',__DIR__.'/../Controller/');//控制器
	
	define('d_path',__DIR__.'/../Driver/');//驱动
	

    //项目名称
    define('PROJECT', 'db');


    //配置类
    class config{

    	//返回系统驱动的类名
    	static public function sysDriver(){
    		
    		//mysql,图片验证码，正则表达式，redis,邮件
    		return array('db','verify','preg','myRedis','email','myMongo','mypdo');
    	
    	}

    }


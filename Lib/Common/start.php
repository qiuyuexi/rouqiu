<?php
	@session_start();
	//error_reporting(0);
	header("Content-type:text/html;charset=utf-8");
	define('c_path',__DIR__.'/../Controller/');
	date_default_timezone_set('Asia/Shanghai');
	require_once(__DIR__.'/../Config/config.php');
	require_once(__DIR__.'/../Common/common.php');
	require_once(__DIR__.'/../Driver/db.driver.php');
	require_once(__DIR__.'/../Driver/errorHandle.php');
	require_once(__DIR__.'/../Driver/verify.php');
	require_once(__DIR__.'/../Driver/preg.driver.php');
	require_once(__DIR__.'/../Driver/redis.driver.php');
	require_once(__DIR__.'/../Driver/mail.driver.php');
	require_once(__DIR__.'/../Vendor/PHPExcel.php');
	require_once(__DIR__.'/../Vendor/PHPExcel/IOFactory.php');
	function load($class_name){

		require_once(c_path.$class_name.'.class.php');
	}
	spl_autoload_register('load');


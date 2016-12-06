<?php

namespace Driver;

/**
*
*@descripe 正则表达式控制器
*/

class preg{

	private static $conn;

	public function index(){


	}
	
	/**
	 * [phone description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 */
	
	public function phone($subject){

		$pattern = '/^1[3578]{1}\d{9}$/';
		
		return preg_match($pattern, $subject);
	}


	/**
	 * [chinese description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 */
	
	public function chinese($subject){
		$pattern = '/^[\u4e00-\u9fa5]+$/';
		return preg_match($pattern, $subject);
	}

	
	/**
	 * [idCard description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 */
	
	public function idCard($subject){
		$pattern = '/^(\d{15}|\d{17}[0-9|X])$/';
		return preg_match($pattern, $subject);
	}


	/**
	 * [checkNum description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 */
	
	public function number($subject){
		$pattern = '/^[0-9]$/';
		return preg_match($pattern, $subject);
	}


	/**
	 * [chacater description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 */
	
	public function chacater($subject){
		$pattern = '/^[a-zA-Z]$/';
		return preg_match($pattern, $subject);
	}


	/**
	 * [pwd description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 * @descripe 验证是否满足密码的格式
	 */
	
	public function pwd($subject){
		$pattern = '/^[\w]{6,20}$/';
		return preg_match($pattern, $subject);	
	}


	/**
	 * [email description]
	 * @param  [type] $subject [description]
	 * @return [type]          [description]
	 * @descripe 验证邮箱
	 */
	
	public function email($subject){
		return filter_var($subject,FILTER_VALIDATE_EMAIL);
	}
}
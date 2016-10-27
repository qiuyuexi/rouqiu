<?php

/**
 *所有控制器 继承该类，所有方法的基础返回信息
 */

class BaseController {
	
	protected $conn;//数据库链接

	protected $redis;//redis 链接

	protected $post;//post过来的数据

	protected $preg;//正则表达式 链接

	protected $back_info;//基本返回信息


	public function __construct(){

		$this->conn = db::getConn();

		$this->redis = myRedis::getRedis();

		$this->redis->selectDb(1);//修改选择的数据库

		$this->preg = new preg();

		$this->back_info = array(
			
			'status'=>false,
			'error_code'=>-1,
			'error_msg'=>'data is null',
		
		);

		$post = file_get_contents("php://input");
		
		$this->post = jsonDecode($post);
	}


	public function __destruct(){

		//$this->conn->close();

		//$this->redis->close();
	}


	/**
	 * [__call description]
	 * @param  [type] $methods [description]
	 * @param  [type] $args    [description]
	 * @return [type]          [description]
	 */
	
	public function __call($methods,$args){

		writeLog($_GET);
		$args = implode(',',$args);
		
		writeLog("unknow $methods,$args");

		$this->back_info = $this->invaildInterface();
		echo jsonEncode($this->back_info);

		return false;
	}


	public function index(){

		echo 'what are you nong sha lei';
	}


	/**
	 * [seuccess description]
	 * @return [type] [description]
	 * #descripe 返回成功的信息
	 */
	
	public function success(){

		$back_info = array(

			'status'=>true,
			'error_code'=>0,
			'error_msg'=>'ok',

		);
		
		return $back_info;
	}


	/**
	 * [sqlError description]
	 * @return [type] [description]
	 * @descripe 数据库执行出错的返回信息
	 */
	
	public function errSql(){

		$back_info = array(

			'status'=>false,
			'error_code'=>-3,
			'error_msg'=>'error sql',
		
		);

		return $back_info;
	}


	/**
	 * [dataIsNull description]
	 * @return [type] [description]
	 * @descripe 数据为空时的返回信息
	 */
	
	public function dataIsNull(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-1,
			'error_msg'=>'data is null',
		
		);

		return $back_info;
	}


	/**
	 * [repaetName description]
	 * @return [type] [description]
	 * @descripe 名称重复
	 */
	
	public function repeatName(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-500,
			'error_msg'=>'repeat name ',
		
		);

		return $back_info;
	}


		/**
	 * [repaetName description]
	 * @return [type] [description]
	 * @descripe 名称重复
	 */
	
	public function repeatEmail(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-502,
			'error_msg'=>'repeatEmail  ',
		
		);

		return $back_info;
	}


	/**
	 * 
	 * [repaetName description]
	 * @return [type] [description]
	 * @descripe 名称重复
	 */
	
	public function repeatDept(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-511,
			'error_msg'=>'repeatdept  ',
		
		);

		return $back_info;
	}

	/**
	 * [errPhoneFormat description]
	 * @return [type] [description]
	 * @descripe 错误的电话格式
	 */
	
	public function errPhoneFormat(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-11,
			'error_msg'=>'error phone ',
		);

		return $back_info;	
	}


	/**
	 * [repaetName description]
	 * @return [type] [description]
	 * @descripe 名称重复
	 */
	
	public function repeatPhone(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-501,
			'error_msg'=>'repeat phone',
		
		);

		return $back_info;
	}


	public function errRedis(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-31,
			'error_msg'=>'errRedis',
	
		);

		return $back_info;	
	}

	/**
	 * [errIdCardFormat description]
	 * @return [type] [description]
	 * @descripe 错误的身份证格式
	 */
	
	public function errIdCardFormat(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-12,
			'error_msg'=>'error idCard  ',
	
		);

		return $back_info;
	}


	/**
	 * [errEmailFormat description]
	 * @return [type] [description]
	 * @descripe 错误的邮件格式
	 */
	
	public function errEmailFormat(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-14,
			'error_msg'=>'error email',
		
		);

		return $back_info;
	}


	/**
	 * [errAddressFormat description]
	 * @return [type] [description]
	 * #descripe 错误的地址格式
	 */
	
	public function errAddressFormat(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-15,
			'error_msg'=>'error address',
		
		);

		return $back_info;	
	}


	/**
	 * [invaildPhone description]
	 * @return [type] [description]
	 * @descripe 无效的电话号码
	 */
	
	public function invaildPhone(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-100001,
			'error_msg'=>'invaildPhone',
		
		);

		return $back_info;	
	}


	/**
	 * [errPwd description]
	 * @return [type] [description]
	 * @descripe 密码错误
	 */
	
	public function invaildPwd(){
		
		$back_info = array(
			
			'status'=>false,
			'error_code'=>-100002,
			'error_msg'=>'invaildPwd',
		
		);

		return $back_info;	
	}


	/**
	 * [invaildToken description]
	 * @return [type] [description]
	 * @descripe 无效的令牌
	 */
	
	public function invaildToken(){
		
		$back_info = array(
			
			'status'=>false,
			'error_code'=>-100003,
			'error_msg'=>'invaildToken',
		
		);

		return $back_info;	
	}	


	/**
	 * [invaildEmail description]
	 * @return [type] [description]
	 * @DESCRIPE 无效的邮箱
	 */
	
	public function invaildEmail(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-100007,
			'error_msg'=>'invaildEmail',
		
		);

		return $back_info;		
	}


	/**
	 * [invaildCode description]
	 * @return [type] [description]
	 * @descripe 无效的验证码
	 */
	
	public function invaildCode(){
			
		$back_info = array(
			
			'status'=>false,
			'error_code'=>-100008,
			'error_msg'=>'invaildCode',
		
		);

		return $back_info;		
	}


	/**
	 * [invaildInterface description]
	 * @return [type] [description]
	 * @descripe 该接口不存在
	 */
	
	public function invaildInterface(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-100009,
			'error_msg'=>'invaildInterface',
		
		);

		return $back_info;	
	}	
	

	public function invaildCustom(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-1000011,
			'error_msg'=>'invaildCustom',
		
		);

		return $back_info;		
	}

	/**
	 * [existLeader description]
	 * @return [type] [description]
	 * @descripe 该部门已有负责人
	 */
	
	public function existLeader(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-510,
			'error_msg'=>'invaildInterface',
		
		);

		return $back_info;	
	}

	/**
	 * [checkToken description]
	 * @param  [type] $token [description]
	 * @return [type]        [description]
	 * @descripe 验证token
	 */
	
	public function checkToken($token = NULL){

		$status = false;

		//writeLog($token);
		if(isset($token) && is_string($token) && !is_null($token)){
			
			$info = $this->redis->getString($token);
		//	writeLog($info);
			if(isset($info) && $info != 'null' ){

				$status = true;

			}else{

				$this->redis->deleteString($token);
			}

		}
		return $status;
	}


	/**
	 * [encryptedPwd description]
	 * @param  [type] $pwd    [description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 * @descripe对密码进行加密
	 */
	
	public function encryptedPwd($pwd = NULL,$string = NULL){

		return md5($pwd);

	}


	/**
	 * [queryCompanyId description]
	 * @param  [type] $custom_id [description]
	 * @return [type]            [description]
	 * @descripe 根据用户的id 查询对应的公司
	 */
	
	public function queryCompanyId($custom_id = NULL){
		
		//查询公司信息
		$sql = "select company_id from custom_company where custom_id = %d ";
		$sql = sprintf($sql,$custom_id);
		$company_id = $this->conn->find($sql);
		$company_id = isset($company_id['company_id']) ? $company_id['company_id'] : 1;
		
		return $company_id;
	}


	/**
	 * [queryDeptId description]
	 * @param  [type] $custom_id [description]
	 * @return [type]            [description]
	 * @descripe 查询客户所在的部门，可能一个人属于多部门
	 */
	
	public function queryDeptId($custom_id = NULL){

		$sql = "select dept_id from custom_dept where custom_id = %d ";
		$sql = sprintf($sql,$custom_id);
		$dept_id = $this->conn->select($sql);
		$dept_id = isset($dept_id) ? $dept_id : array();

		return $dept_id;
	}


	/**
	 * [queryLoanTyoe description]
	 * @param  [type] $company_id [description]
	 * @return [type]             [description]
	 * @descripe 查询公司提供的贷款类型
	 */
	
	public function queryLoanType($company_id = NULL){

		//查询公司的贷款性质
		$sql = "select  loan_type from company  where id = %d ";
		$sql = sprintf($sql,$company_id);
		//echo $sql;
		$loan_type = $this->conn->find($sql);
		$loan_type = isset($loan_type['loan_type']) ? $loan_type['loan_type'] : NULL;
		return $loan_type;
	}


	/**
	 * [getCustomId description]
	 * @param  [type] $token [description]
	 * @return [type]        [description]
	 * @descripe 根据token 查询对应的用户id
	 */
	
	public function getCustomId($token = NULL){

		//个人信息
		$info = $this->redis->getString($token);
		$info = isset($info) ? jsonDecode($info) : array();
		$custom_id = isset($info['id']) ? $info['id'] : 2; 
		
		return $custom_id;
	}


	/**
	 * [uploadFileFailed description]
	 * @return [type] [description]
	 * @oci_new_descriptor( 上传文件失败)
	 */
	public function uploadFileFailed(){

		$back_info = array(
			
			'status'=>false,
			'error_code'=>-401,
			'error_msg'=>'uploadFileFailed',
		
		);

		return $back_info;	
	}

}

<?

namespace Driver;

//引入PHPMailer的核心文件 使用require_once包含避免出现PHPMailer类重复定义的警告
require_once(__DIR__."/../Vendor/PHPMailer/PHPMailerAutoload.php");

/**
 * Class email
 * @package Driver
 */

class email {
		
	private $mail;

	public function __construct($send_address = 'xx@qq.com'){
		
		//示例化PHPMailer核心类

		$this->mail = new \PHPMailer($send_address);
		
		$this->mail->isSMTP();

		$this->mail->SMTPDebug = 0;

		$this->mail->isSMTP();

		$this->mail->SMTPAuth=true;
		
		$this->mail->Host = 'smtp.163.com';

		//设置使用ssl加密方式登录鉴权
		$this->mail->SMTPSecure = 'ssl';

		//设置ssl连接smtp服务器的远程服务器端口号 可选465或587
		$this->mail->Port = 465;

		//设置发送的邮件的编码 可选GB2312 我喜欢utf-8 据说utf8在某些客户端收信下会乱码
		$this->mail->CharSet = 'UTF-8';

		//设置发件人姓名（昵称） 任意内容，显示在收件人邮件的发件人邮箱地址前的发件人姓名
		$this->mail->FromName = 'z8853797';

		//smtp登录的账号 
		$this->mail->Username ='xx@163.com';

		//smtp登录的密码 
		$this->mail->Password = 'xx';

		//设置发件人邮箱地址 这里填入上述提到的“发件人邮箱”
		$this->mail->From = 'xx@163.com';

		//邮件正文是否为html编码 注意此处是一个方法 不再是属性 true或false
		$this->mail->isHTML(true); 


	}


	public function setAttach($attach = array()){

		//为该邮件添加附件 该方法也有两个参数 第一个参数为附件存放的目录（相对目录、或绝对目录均可） 第二参数为在邮件附件中该附件的名称
		
		foreach ($attach as $key => $value) {
			
			/**/
			$this->mail->addAttachment($value['path'],$value['name']);
		
		}

	}


	/**
	 * [setBody description]
	 *@descripe
	 *添加邮件正文 上方将isHTML设置成了true，则可以是完整的html字符串 如：使用file_get_contents函数读取本地的html文件 
	 */
	public function setBody($body){

		$this->mail->Body = $body;
	
	}


	/**
	 * [setAddress description]
	 * @param [type] $send_address [description]
	 * @descripe 
	 * //设置收件人邮箱地址 该方法有两个参数 第一个参数为收件人邮箱地址 第二参数为给该地址设置的昵称 不同的邮箱系统会自动进行处理变动 
	 */
	
	public function setAddress($send_address = NULL){
	
		$this->mail->addAddress($send_address);
	
	}


	/**
	 * [setSubject description]
	 * @param [type] $title [description]
	 * 添加该邮件的主题
	 */
	public function setSubject($title = NULL){

		$this->mail->Subject = $title;
	}



	/**
	 * [send description]
	 * @return [type] [description]
	 * @descripe 发送邮件
	 */
	
	public function send(){

		//发送命令 返回布尔值 
		//PS：经过测试，要是收件人不存在，若不出现错误依然返回true 也就是说在发送之前 自己需要些方法实现检测该邮箱是否真实有效
		$status = $this->mail->send();

		//简单的判断与提示信息
	
		if($status) {
	
			 return 1;
		
		}else{

		 	return 0;
		 	writeLog($this->mail->ErrorInfo);

		}
	}

} 
